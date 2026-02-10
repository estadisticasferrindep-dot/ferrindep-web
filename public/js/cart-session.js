/**
 * CartSession - Frontend Logic for Session-based Cart
 * Handles AJAX requests to update cart quantity and updates UI accordingly.
 */

const CartSession = {
    csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',

    /**
     * Set quantity for a presentation.
     * @param {int} presentacionId 
     * @param {int} qty 
     * @param {HTMLElement} btnElement Optional: button that triggered action to show loading
     */
    setQty: function (presentacionId, qty, btnElement = null) {
        if (!this.csrfToken) {
            console.error("CSRF Token Missing");
            return;
        }
        if (qty < 0) qty = 0;

        // UI Feedback: Disable inputs/buttons related to this presentation
        this.toggleLoading(presentacionId, true);

        fetch('/carrito/session/set-qty', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                presentacion_id: presentacionId,
                qty: qty
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.updateCartSummary(data.summary);
                    // Optional: Show success feedback (toast, tick, etc.)
                } else {
                    console.error('Error modifying cart', data);
                    alert('No se pudo actualizar el carrito. Intente nuevamente.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error de conexión.');
            })
            .finally(() => {
                this.toggleLoading(presentacionId, false);
            });
    },

    /**
     * Update the floating cart summary or header badge.
     */
    updateCartSummary: function (summary) {
        // Implement logic to update header cart count if elements exist
        const countElements = document.querySelectorAll('.cart-count-badge');
        countElements.forEach(el => {
            el.innerText = summary.count;
            el.style.display = summary.count > 0 ? 'inline-block' : 'none';
        });

        // You could also update a total price somewhere
        console.log('Cart Summary Updated:', summary);
    },

    /**
     * Disable/Enable controls while loading.
     */
    toggleLoading: function (presentacionId, isLoading) {
        const container = document.querySelector(`.qty-controls[data-id="${presentacionId}"]`);
        if (container) {
            const inputs = container.querySelectorAll('button, input');
            inputs.forEach(input => {
                input.disabled = isLoading;
            });
            container.style.opacity = isLoading ? '0.5' : '1';
        }
    },

    /**
     * Helper to increment/decrement from UI
     */
    changeQty: function (presentacionId, delta) {
        const input = document.querySelector(`input.qty-input[data-id="${presentacionId}"]`);
        if (!input) return;

        let current = parseInt(input.value) || 0;
        let next = current + delta;
        if (next < 0) next = 0;

        input.value = next; // Optimistic UI update
        this.setQty(presentacionId, next);
    },

    // --- Shipping Logic ---
    currentSubtotal: 0,

    initShipping: function (inputId, costId, btnId) {
        const input = document.getElementById('cart-gps-input');
        const costEl = document.getElementById(costId);
        const btn = document.getElementById(btnId);
        const destInput = document.getElementById('shipping-dest-id');
        const detectedLabel = document.getElementById('cart-detected-location');
        const locationStatus = document.getElementById('cart-location-status');

        // Initial Calculation if session exists
        if (destInput && destInput.value) {
            this.calculateShipping(destInput.value, costEl, btn);
        }

        // SAFE CHECK FOR GOOGLE MAPS
        if (!input || typeof google === 'undefined' || !google.maps || !google.maps.places) {
            console.warn("Google Maps Places library not loaded or input missing.");
            return;
        }

        const options = {
            types: ['geocode'],
            componentRestrictions: { country: 'ar' },
            fields: ['address_components', 'geometry']
        };

        const autocomplete = new google.maps.places.Autocomplete(input, options);

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            let city = '';
            let partido = '';
            let region = '';

            // Extract Components
            // We prioritize: Locality (City) -> Sublocality -> Administrative Area Level 2 (Partido)
            for (const component of place.address_components) {
                const types = component.types;

                if (types.includes('locality')) {
                    city = component.long_name;
                }

                if (!city && types.includes('sublocality')) {
                    city = component.long_name;
                }

                if (types.includes('administrative_area_level_2')) {
                    partido = component.long_name;
                }

                if (types.includes('administrative_area_level_1')) {
                    region = component.long_name;
                }
            }

            // Fallback: If no city found, use the first component (usually specific neighborhood)
            if (!city && place.address_components.length > 0) {
                city = place.address_components[0].long_name;
            }

            // UI Update - Show "City (Partido)" or just "City"
            if (detectedLabel) {
                let displayText = city;
                if (partido && partido !== city) {
                    displayText += ` (${partido})`;
                }
                detectedLabel.innerText = displayText;
            }

            if (locationStatus) locationStatus.style.display = 'block';
            if (costEl) costEl.innerText = 'Calculando...';

            console.log("Cart Autocomplete:", { city, partido, region });

            // Send to Backend to Update Session & Calculate
            fetch('/web/gps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ manual_city: city, manual_region: region, manual_partido: partido })
            })
                .then(r => r.json())
                .then(data => {
                    if (destInput && data.mapped_id) destInput.value = data.mapped_id;
                    this.calculateShipping(data.mapped_id, costEl, btn);
                })
                .catch(err => {
                    console.error(err);
                    if (costEl) costEl.innerText = 'Error';
                });
        });
    },

    calculateShipping: function (val, costEl, btn) {
        if (costEl) costEl.innerText = 'Calculando...';

        // We send the ID if available, but the Controller will prioritize Session GPS 
        fetch('/carrito/session/calculate-shipping', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ destino_id: val })
        })
            .then(r => r.json())
            .then(data => {
                if (data.costo_envio !== undefined) {
                    if (costEl) costEl.innerText = '$ ' + new Intl.NumberFormat('es-AR').format(data.costo_envio);
                    this.updateCheckoutLink(btn, data.costo_envio);
                } else if (data.tipo === 'flex_free') {
                    if (costEl) costEl.innerText = 'Envío Gratis';
                    this.updateCheckoutLink(btn, 0);
                } else {
                    if (costEl) costEl.innerText = 'Consultar';
                    console.error(data.error);
                }
            })
            .catch(e => {
                console.error(e);
                if (costEl) costEl.innerText = 'Error';
            });

    },

    updateCheckoutLink: function (btn, shippingCost) {
        if (!btn) return;
        const total = this.currentSubtotal + shippingCost;

        let baseUrl = btn.dataset.url;
        if (!baseUrl) return;

        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('total', total);
        url.searchParams.set('envio', shippingCost);

        // If we are redirecting to finalizar_compra, maybe we want 'envio_calculado' instead of 'envio' 
        // Based on finalizar_compra.blade.php: value="{{ request('envio') }}" and value="{{ request('total') }}"
        // It uses 'envio' for cost.
        btn.href = url.toString();

        // Update Total Display
        const totalDisplay = document.getElementById('cart-total-display');
        if (totalDisplay) {
            totalDisplay.innerText = '$ ' + new Intl.NumberFormat('es-AR').format(total);
        }
    }
};

// Initialize if needed
document.addEventListener('DOMContentLoaded', function () {
    // Attach global listeners if preferred, or rely on onclick attributes
});

// Expose to window for inline onclicks
window.CartSession = CartSession;
