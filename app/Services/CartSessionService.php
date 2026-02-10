<?php

namespace App\Services;

use App\Models\Presentacion;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CartSessionService
{
    protected $sessionKey = 'cart_session_items';
    protected $activityKey = 'cart_last_activity';

    /**
     * Cart expiration time in minutes.
     * After this many minutes of inactivity, the cart is automatically cleared.
     */
    protected $expirationMinutes = 60;

    /**
     * Get all items in the session cart.
     * Structure: [ presentacion_id => ['qty' => int, 'price' => float, 'name' => string, 'product_id' => int] ]
     */
    public function getCart()
    {
        // Check if cart has expired due to inactivity
        $lastActivity = Session::get($this->activityKey);
        if ($lastActivity) {
            $minutesSince = Carbon::parse($lastActivity)->diffInMinutes(Carbon::now());
            if ($minutesSince >= $this->expirationMinutes) {
                // Cart expired — clear it silently
                Session::forget($this->sessionKey);
                Session::forget($this->activityKey);
                return [];
            }
        }

        return Session::get($this->sessionKey, []);
    }

    /**
     * Set absolute quantity for a presentation.
     * Source of truth for price is the database.
     */
    public function setQty($presentacionId, $qty)
    {
        $cart = $this->getCart();

        // Validate Quantity
        $qty = (int) $qty;
        if ($qty <= 0) {
            $this->remove($presentacionId);
            return $this->getCart();
        }

        // Limit Max Quantity (Safety)
        if ($qty > 9999) {
            $qty = 9999;
        }

        // Fetch Presentation from DB to ensure validity and price
        $presentacion = Presentacion::with('producto')->find($presentacionId);

        if (!$presentacion) {
            return $cart; // Or throw exception / error
        }

        // Update or Add Item
        $cart[$presentacionId] = [
            'presentacion_id' => $presentacion->id,
            'qty' => $qty,
            'precio' => $presentacion->precio, // Assuming 'precio' is the column
            'nombre' => $presentacion->nombre ?? $presentacion->medida, // Fallback if name varies
            'producto_id' => $presentacion->producto_id,
            'producto_nombre' => $presentacion->producto ? $presentacion->producto->nombre : 'Producto',
            'imagen_url' => $presentacion->producto ? $presentacion->producto->imagen_url : null, // Added Image URL

            // Metadata for Itemspedido
            'medidas' => $presentacion->producto->medidas->medidas ?? null,
            'espesor' => $presentacion->producto->espesor->espesor ?? null,
            'ancho' => $presentacion->producto->categoria->nombre ?? null, // Categoria assumes usage as width in legacy
            'familia' => $presentacion->producto->familia->nombre ?? null,
            'metros' => $presentacion->metros ?? $presentacion->medida ?? null,
            'conNombre' => $presentacion->producto->con_nombre ?? 0,
            'anulaEnvio' => $presentacion->producto->anular_envio ?? 0,
            'peso' => $presentacion->peso ?? 0,
        ];

        Session::put($this->sessionKey, $cart);
        $this->touchActivity();

        return $cart;
    }

    /**
     * Remove an item from the cart.
     */
    public function remove($presentacionId)
    {
        $cart = $this->getCart();

        if (isset($cart[$presentacionId])) {
            unset($cart[$presentacionId]);
            Session::put($this->sessionKey, $cart);
            $this->touchActivity();
        }

        return $cart;
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        Session::forget($this->sessionKey);
        Session::forget($this->activityKey);
    }

    /**
     * Update the last activity timestamp for the cart.
     */
    protected function touchActivity()
    {
        Session::put($this->activityKey, Carbon::now()->toDateTimeString());
    }

    /**
     * Get summary: total count and total amount.
     */
    public function summary()
    {
        $cart = $this->getCart();
        $count = 0;
        $total = 0;

        foreach ($cart as $item) {
            $count += $item['qty'];
            $total += $item['qty'] * $item['precio'];
        }

        return [
            'count' => $count,
            'total' => $total,
            'items' => $cart
        ];
    }
}
