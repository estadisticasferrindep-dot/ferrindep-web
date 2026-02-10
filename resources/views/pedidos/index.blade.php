@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <br><br>

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('web.mis_compras') }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Ir a Mis Compras
                    </a>
                </div>

                <div class="card" style="margin-top:15px;">
                    <div class="card-body p-0">

                        {{-- Tabla con id explícito --}}
                        <table id="pedidos-table" class="table">
                            <thead style="color:#03224e">
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Código</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Detalle</th>
                                    <th scope="col">Estado</th> {{-- NUEVO --}}
                                    <th scope="col">Medio de pago</th>
                                    <th scope="col">Envío</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pedidos as $pedido)
                                    @php
                                        $codigoPedido = $pedido->codigo
                                            ?? $pedido->order_code
                                            ?? $pedido->numero
                                            ?? $pedido->uuid
                                            ?? ('FD-' . str_pad((string) ($pedido->id ?? 0), 6, '0', STR_PAD_LEFT));
                                    @endphp
                                    <tr @if($pedido->estado_personalizado == 'Entregado') style="background-color: #d4edda;"
                                    @endif>
                                        <td>{{ optional($pedido->created_at)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d H:i') }}
                                        </td>
                                        <td style="white-space:nowrap;">
                                            <span
                                                style="font-family: 'Consolas', monospace; font-weight: 700; color: #004085; background-color: #cce5ff; padding: 4px 8px; border-radius: 4px; font-size: 0.95rem;">
                                                {{ $codigoPedido }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $pedido->usuario_nombre }}
                                            @if($pedido->celular || $pedido->usuario_telefono)
                                                @php
                                                    $rawPhone = $pedido->celular ?? $pedido->usuario_telefono;
                                                    // Sanitize: Only numbers
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);

                                                    // Basic Argentine heuristics
                                                    // If local mobile (10 digits, e.g., 3513110702), prepend 549
                                                    if (strlen($cleanPhone) == 10) {
                                                        $waPhone = '549' . $cleanPhone;
                                                    } elseif (substr($cleanPhone, 0, 2) == '54' && strlen($cleanPhone) == 12) {
                                                        // Already has 54 but missing 9? (Less common mobile, but assume landline or broken mobile)
                                                        // If it's meant to be mobile it needs 9 after 54. 
                                                        // Let's just use it as is if it starts with 54, user might have put it right.
                                                        // Actually, 54 + 10 digits = 12 digits. 
                                                        // Commonly mobiles are 54 9 + 10 = 13 digits.
                                                        // Let's safe bet: if 10 digits, add 549. Otherwise use raw cleaned.
                                                        $waPhone = $cleanPhone;
                                                    } else {
                                                        $waPhone = $cleanPhone;
                                                    }

                                                    // Message
                                                    $waMsg = "Hola " . explode(' ', trim($pedido->usuario_nombre))[0] . ", tu pedido #{$codigoPedido} ya está listo para retirar en Ferrindep. Te esperamos!";
                                                    $waUrl = "https://wa.me/{$waPhone}?text=" . urlencode($waMsg);
                                                @endphp

                                                <div
                                                    style="font-size: 0.85rem; color: #6c757d; margin-top: 2px; display: flex; align-items: center;">
                                                    <i class="fas fa-phone-alt" style="font-size: 0.8em; margin-right: 4px;"></i>
                                                    <span style="margin-right: 4px;">{{ $rawPhone }}</span>

                                                    {{-- COPY BUTTON --}}
                                                    <button class="btn btn-link btn-sm p-0 text-muted"
                                                        onclick="copyToClipboard('{{ $rawPhone }}', this)" title="Copiar"
                                                        style="line-height: 1; margin-right: 6px;">
                                                        <i class="far fa-copy"></i>
                                                    </button>

                                                    {{-- WHATSAPP BUTTON --}}
                                                    <a href="{{ $waUrl }}" target="_blank" class="text-success"
                                                        title="Notificar por WhatsApp" style="line-height: 1;">
                                                        <i class="fab fa-whatsapp" style="font-size: 1.1em;"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            {{-- EMAIL DISPLAY --}}
                                            @php
                                                $email = $pedido->email ?? $pedido->usuario_email ?? optional($pedido->user)->email;
                                            @endphp
                                            @if($email)
                                                <div
                                                    style="font-size: 0.85rem; color: #6c757d; margin-top: 2px; display: flex; align-items: center;">
                                                    <i class="far fa-envelope" style="font-size: 0.9em; margin-right: 5px;"></i>
                                                    <span style="font-size: 0.85rem;">{{ $email }}</span>
                                                    {{-- COPY BUTTON --}}
                                                    <button class="btn btn-link btn-sm p-0 text-muted"
                                                        onclick="copyToClipboard('{{ $email }}', this)" title="Copiar"
                                                        style="line-height: 1; margin-left: 5px;">
                                                        <i class="far fa-copy"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                        <td>${{ number_format((float) $pedido->total, 0, ',', '.') }}</td>
                                        <td>
                                            @if (!empty($pedido->itemsPedidos))
                                                <ul class="mb-0 ps-3">
                                                    @foreach ($pedido->itemsPedidos as $item)
                                                        @if($item->con_nombre)
                                                            <li style="margin-bottom: 5px; display: flex; align-items: center;">
                                                                @if(optional($item->producto)->imagen)
                                                                    <img src="{{ asset(Storage::url($item->producto->imagen)) }}"
                                                                        style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px; margin-right: 8px;"
                                                                        alt="img">
                                                                @endif
                                                                <div>
                                                                    <strong>{{ $item->cantidad }}
                                                                        {{ $item->cantidad == 1 ? 'rollo' : 'rollos' }}</strong>
                                                                    {{ $item->nombre }}
                                                                    @if($item->ancho)
                                                                        <span class="text-muted"
                                                                            style="font-size:0.9em">({{ $item->ancho }})</span>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @else
                                                            <li style="margin-bottom: 5px; display: flex; align-items: flex-start;">
                                                                @if(optional($item->producto)->imagen)
                                                                    <img src="{{ asset(Storage::url($item->producto->imagen)) }}"
                                                                        style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px; margin-right: 8px; margin-top: 2px;"
                                                                        alt="img">
                                                                @endif
                                                                <div>
                                                                    <strong>{{ $item->cantidad }}
                                                                        {{ $item->cantidad == 1 ? 'rollo' : 'rollos' }}</strong>
                                                                    {{ $item->medidas }}
                                                                    @if($item->espesor)
                                                                        <span class="text-muted">({{ $item->espesor }})</span>
                                                                    @endif

                                                                    @php
                                                                        // Formatear dimensiones si es numérico (mallas)
                                                                        $dims = '';
                                                                        if (is_numeric($item->nombre)) {
                                                                            $val = floatval($item->nombre);
                                                                            $dims = ($val >= 100) ? ($val / 100) . 'm' : $val . 'cm';
                                                                        } else {
                                                                            $dims = $item->nombre;
                                                                        }
                                                                    @endphp
                                                                    <span style="font-size:0.9em">
                                                                        - {{ $dims }}
                                                                        @if($item->ancho)
                                                                            x {{ $item->ancho }}cm ancho
                                                                        @endif
                                                                    </span>
                                                                    <div style="font-size:0.85em; color:#666; margin-top:2px;">
                                                                        ({{ $item->metros }}m)
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        {{-- COLUMNA ESTADO --}}
                                        <td>
                                            <select class="form-control form-control-sm status-selector"
                                                data-id="{{ $pedido->id }}" style="width: 140px;">
                                                <option value="" {{ !$pedido->estado_personalizado ? 'selected' : '' }}>
                                                    Pendiente</option>
                                                <option value="En preparación" {{ $pedido->estado_personalizado == 'En preparación' ? 'selected' : '' }}>En preparación</option>
                                                <option value="Preparado, aguardando pago" {{ $pedido->estado_personalizado == 'Preparado, aguardando pago' ? 'selected' : '' }}>Preparado, aguardando pago</option>
                                                <option value="Listo para retirar" {{ $pedido->estado_personalizado == 'Listo para retirar' ? 'selected' : '' }}>Listo para retirar</option>
                                                <option value="Despachado" {{ $pedido->estado_personalizado == 'Despachado' ? 'selected' : '' }}>Despachado</option>
                                                <option value="Enviado" {{ $pedido->estado_personalizado == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                                                <option value="En camino" {{ $pedido->estado_personalizado == 'En camino' ? 'selected' : '' }}>En camino</option>
                                                <option value="Retiró transporte" {{ $pedido->estado_personalizado == 'Retiró transporte' ? 'selected' : '' }}>Retiró transporte</option>
                                                <option value="Pago acreditado" {{ $pedido->estado_personalizado == 'Pago acreditado' ? 'selected' : '' }}>Pago acreditado</option>
                                                <option value="Pago acreditado, pedido en preparación" {{ $pedido->estado_personalizado == 'Pago acreditado, pedido en preparación' ? 'selected' : '' }}>Pago acreditado, pedido en preparación</option>
                                                <option value="Entregado" {{ $pedido->estado_personalizado == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                                                <option value="Anulado" {{ $pedido->estado_personalizado == 'Anulado' ? 'selected' : '' }}>Anulado</option>
                                            </select>

                                            {{-- HISTORY DISPLAY --}}
                                            @if($pedido->historial_estado && is_array($pedido->historial_estado))
                                                <div style="font-size:0.75rem; color:#666; margin-top:4px; line-height:1.2;">
                                                    @php
                                                        $lastStatus = end($pedido->historial_estado);
                                                    @endphp
                                                    @if($lastStatus && isset($lastStatus['fecha']))
                                                        <i class="far fa-clock"></i> {{ $lastStatus['fecha'] }}
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td style="text-transform: uppercase">{{ $pedido->pago }}</td>
                                        <td style="text-transform: uppercase">
                                            @if(strtolower($pedido->envio) == 'fabrica')
                                                <span class="badge bg-light text-dark border">Retira</span>
                                            @else
                                                <span class="badge bg-primary">Enviar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="display:flex; align-items:center">
                                                <button type="button" class="btn btn-primary" style="margin-right:5px;">
                                                    <a style="color:white;" href="{{ route('pedidos.edit', $pedido) }}">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                </button>
                                                <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('¿Estas seguro?');"
                                                        class="btn btn-danger">
                                                        <a style="color:white;" href="#"><i class="fas fa-trash-alt"></i></a>
                                                    </button>
                                                </form>
                                                {{-- Botón Reenviar Email --}}
                                                <button type="button" class="btn btn-warning btn-sm btn-resend-email"
                                                    style="margin-left:5px; color:white; background-color: #6f42c1; border-color: #6f42c1;"
                                                    data-id="{{ $pedido->id }}" title="Reenviar Email Confirmación">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="nota-row-{{ $pedido->id }}">
                                        <td colspan="9" style="border-top: none; padding: 0 0.75rem 5px 0.75rem;">
                                            <input type="text" class="form-control form-control-sm border-0 nota-input"
                                                data-id="{{ $pedido->id }}" value="{{ $pedido->nota }}"
                                                placeholder="Agregar nota..."
                                                style="background-color: #000; color: #fff; font-size: 0.85rem; height: 26px; box-shadow: none;">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">No hay pedidos para mostrar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9">
                                        @if(method_exists($pedidos, 'links'))
                                            <div class="px-3 pb-3 d-flex justify-content-center custom-pagination-container">
                                                {{ $pedidos->links() }}
                                            </div>
                                            <style>
                                                .custom-pagination-container .pagination {
                                                    justify-content: center;
                                                    /* Asegura centrado flex */
                                                    margin-bottom: 0;
                                                }

                                                .custom-pagination-container .page-link {
                                                    font-size: 0.8rem;
                                                    /* Letra más chica */
                                                    padding: 0.25rem 0.6rem;
                                                    /* Botones más compactos */
                                                    line-height: 1.5;
                                                }

                                                .custom-pagination-container .page-item.active .page-link {
                                                    background-color: #6c757d;
                                                    /* Gris oscuro neutro */
                                                    border-color: #6c757d;
                                                }

                                                border-color: #6c757d;
                                                }

                                                /* DEFENSIVE CSS: Hide any rogue pagination that might be appearing elsewhere */
                                                .pagination {
                                                    display: none;
                                                }

                                                .custom-pagination-container .pagination {
                                                    display: flex !important;
                                                    /* Force show only ours */
                                                }

                                                /* Force footer to stay at bottom */
                                                tfoot {
                                                    display: table-footer-group !important;
                                                }
                                            </style>
                                            <script>
                                                // JS NUKE SQUAD for Phantom Pagination
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    // 1. Find standard Bootstrap/Tailwind pagination structures
                                                    var badPags = document.querySelectorAll('nav[role="navigation"], ul.pagination');
                                                    badPags.forEach(function (el) {
                                                        // Ensure we don't kill our own nice pagination
                                                        if (!el.closest('.custom-pagination-container')) {
                                                            el.remove(); // DESTROY IT
                                                            console.log("Phantom pagination removed from DOM.");
                                                        }
                                                    });
                                                });
                                            </script>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script para Status Update via AJAX --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // LOGICA PARA UPDATE STATUS
            const selectors = document.querySelectorAll('.status-selector');
            selectors.forEach(sel => {
                sel.addEventListener('change', function () {
                    const pedidoId = this.dataset.id;
                    const newStatus = this.value;

                    // Feedback visual
                    const originalColor = this.style.backgroundColor;
                    this.style.backgroundColor = '#fff3cd'; // Amarillo mientras carga

                    fetch('{{ route("pedidos.updateStatus") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: pedidoId,
                            status: newStatus
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.style.backgroundColor = '#d4edda'; // Verde éxito
                                setTimeout(() => {
                                    this.style.backgroundColor = ''; // Restaurar
                                    window.location.reload(); // Reload to show history timestamp
                                }, 500);
                            } else {
                                alert('Error al actualizar estado');
                                this.style.backgroundColor = '#f8d7da'; // Rojo error
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error de conexión');
                            this.style.backgroundColor = '#f8d7da';
                        });
                });
            });

            // LOGICA PARA REENVIAR EMAIL
            const emailButtons = document.querySelectorAll('.btn-resend-email');
            emailButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    if (!confirm('¿Seguro que deseas REENVIAR el email de confirmación al cliente?')) return;

                    const pedidoId = this.dataset.id;
                    const originalText = this.innerHTML;

                    // Estado cargando
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('{{ route("pedidos.reenviarEmail") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id: pedidoId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            this.disabled = false;
                            this.innerHTML = originalText;

                            if (data.success) {
                                alert('ÉXITO: ' + data.message);
                            } else {
                                alert('ERROR: ' + data.message);
                            }
                        })
                        .catch(error => {
                            this.disabled = false;
                            this.innerHTML = originalText;
                            console.error('Error:', error);
                            alert('Error de conexión al reenviar email.');
                        });
                });
            });

            // LOGICA PARA NOTAS
            const notaInputs = document.querySelectorAll('.nota-input');
            notaInputs.forEach(input => {
                input.addEventListener('blur', function () {
                    const pedidoId = this.dataset.id;
                    const nota = this.value;

                    // Feedback visual sutil
                    this.style.backgroundColor = '#fff3cd'; // Amarillo

                    fetch('{{ route("pedidos.updateNota") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id: pedidoId, nota: nota })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.style.backgroundColor = '#28a745'; // Green for success
                                this.style.color = '#fff';
                                setTimeout(() => {
                                    this.style.backgroundColor = '#000'; // Return to Black
                                    this.style.color = '#fff';
                                }, 1000);
                            } else {
                                this.style.backgroundColor = '#dc3545'; // Red for error
                                this.style.color = '#fff';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.style.backgroundColor = '#f8d7da';
                        });
                });

                // Enter para guardar/salir
                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        this.blur();
                    }
                });
            });

            // UTILIDAD PARA COPIAR AL PORTAPAPELES
            window.copyToClipboard = function (text, element) {
                if (!text) return;
                navigator.clipboard.writeText(text).then(() => {
                    const originalHtml = element.innerHTML;
                    element.innerHTML = '<i class="fas fa-check text-success"></i>';
                    setTimeout(() => {
                        element.innerHTML = originalHtml;
                    }, 1500);
                }).catch(err => {
                    console.error('Error al copiar: ', err);
                });
            };
        });
    </script>

    {{-- ===== Anti-DataTables (v1 y v2) ===== --}}
    <style>
        /* Ocultar UI de DataTables v1 */
        .dataTables_wrapper,
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        /* Ocultar UI de DataTables v2 */
        .dt-container,
        .dt-length,
        .dt-search,
        .dt-info,
        .dt-paging,
        .dt-processing,
        .dt-scroll {
            display: none !important;
        }
    </style>

    <script>
        (function () {
            function nukeDT() {
                var table = document.getElementById('pedidos-table');
                if (!table) return;

                // --- jQuery DataTables (v1) ---
                if (window.jQuery && (jQuery.fn.DataTable || jQuery.fn.dataTable)) {
                    var $t = jQuery('#pedidos-table');
                    try {
                        var isV1 = (jQuery.fn.DataTable && jQuery.fn.DataTable.isDataTable && jQuery.fn.DataTable.isDataTable($t))
                            || (jQuery.fn.dataTable && jQuery.fn.dataTable.isDataTable && jQuery.fn.dataTable.isDataTable($t));
                        if (isV1) {
                            try { $t.DataTable().destroy(); } catch (e) { try { $t.dataTable().fnDestroy(); } catch (e2) { } }
                        }
                    } catch (e) { }
                }

                // --- DataTables v2 (vanilla) no-jQuery: des-envolver si existe ---
                var wrapV2 = table.closest ? table.closest('.dt-container') : null;
                if (wrapV2) {
                    wrapV2.parentNode.insertBefore(table, wrapV2);
                    wrapV2.remove();
                }

                // --- DataTables v1 wrapper ---
                var wrapV1 = table.closest ? table.closest('.dataTables_wrapper') : null;
                if (wrapV1) {
                    wrapV1.parentNode.insertBefore(table, wrapV1);
                    wrapV1.remove();
                }

                // Limpieza de restos
                table.classList.remove('dataTable', 'dt-table');
                document.querySelectorAll('.dataTables_wrapper, .dt-container').forEach(function (w) { w.remove(); });
            }

            // Ejecutar y reintentar (por si se inicializa tarde)
            document.addEventListener('DOMContentLoaded', function () {
                nukeDT();
                setTimeout(nukeDT, 80);
                setTimeout(nukeDT, 250);
                setTimeout(nukeDT, 800);
                setTimeout(nukeDT, 1500);
            });

            // Vigilar inyecciones futuras de wrappers
            var obs = new MutationObserver(function (muts) {
                var hit = muts.some(m => Array.from(m.addedNodes || []).some(n =>
                    n.nodeType === 1 && (n.classList.contains('dataTables_wrapper') || n.classList.contains('dt-container'))
                ));
                if (hit) nukeDT();
            });
            obs.observe(document.body, { childList: true, subtree: true });
        })();
    </script>
@endsection