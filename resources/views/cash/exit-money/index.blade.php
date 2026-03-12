@extends('layouts.template')

@section('title')
    Egresos
@endsection

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">LISTA DE EGRESOS</h6>
            <div class="input-group-append">
                <a href="{{ route('tenant.cajas.egresos.create') }}" class="btn btn-primary">
                    <div class="lign-items-center d-flex align-items-center">
                        <i class="fas fa-plus pe-1"></i>
                        <p class="mb-0 ml-2">NUEVO</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="card-body p-0 pb-2">
            <form action="{{ route('tenant.cajas.egresos.index') }}" method="GET">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="form-group me-3">
                        <label for="from_date">Desde</label>
                        <input type="date" name="from_date" id="from_date" class="form-control"
                            value="{{ $from_today }}">
                    </div>
                    <div class="form-group">
                        <label for="to_date">Hasta</label>
                        <div class="d-flex align-items-center">
                            <input type="date" name="to_date" id="to_date" class="form-control me-2"
                                value="{{ $to_today }}">

                            <button type="submit" class="btn btn-rounded btn-primary">
                                <i class='bx bx-search-alt-2'></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @include('cash.exit-money.tables.tbl_list_exit_money')
        </div>
    </div>
@endsection

@section('js')
    <script>
        let dtExitMoneys = null;

        document.addEventListener('DOMContentLoaded', () => {
            iniciarDtExitMoneys();
        });

        function iniciarDtExitMoneys() {
            dtExitMoneys = new DataTable('#dt-exit-moneys', {
                processing: true,
                ajax: '{{ route('tenant.cajas.egresos.getExitMoneys') }}',
                columns: [{
                        data: 'id',
                        className: "text-center",
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'cash_book_code',
                        name: 'cash_book_code',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'date',
                        name: 'em.date',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'cost_center_name',
                        name: 'em.cost_center_name',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'supplier_name',
                        name: 's.name',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'number',
                        name: 'em.number',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'payment_method_name',
                        name: 'em.payment_method_name',
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'total',
                        name: 'em.total',
                        className: "text-center",
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            return formatSoles(data);
                        }
                    },
                    {
                        data: 'discount_cash',
                        name: 'em.discount_cash',
                        className: "text-center align-middle",
                        orderable: true,
                        searchable: false,
                        render: function(data) {

                            if (data == 1 || data === true) {
                                return `
                                    <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        <i class="fas fa-cash-register me-1"></i>
                                        Descontado
                                    </span>
                                `;
                            } else {
                                return `
                                    <span class="badge rounded-pill bg-danger-subtle text-danger fw-semibold px-2 py-1">
                                        <i class="fas fa-ban me-1"></i>
                                        No descontado
                                    </span>
                                `;
                            }
                        }
                    },
                    {
                        data: null,
                        searchable: false,
                        className: "text-center",
                        render: function(data) {

                            const pdfUrl = `{{ route('tenant.cajas.egresos.pdf', ':id') }}`
                                .replace(':id', data.id);

                            const editUrl = `{{ route('tenant.cajas.egresos.edit', ':id') }}`
                                .replace(':id', data.id);

                            return `
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary border dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                            href="${pdfUrl}" target="_blank">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                PDF
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                            href="${editUrl}">
                                                <i class="fas fa-pen text-primary"></i>
                                                Editar
                                            </a>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                    onclick="anularExitMoney(${data.id})">
                                                <i class="fas fa-ban"></i>
                                                Anular
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    decimal: "",
                    emptyTable: "No hay datos disponibles en la tabla",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    lengthMenu: "Mostrar _MENU_ registros",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar:",
                    zeroRecords: "No se encontraron registros coincidentes",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                },

                order: [
                    [0, "desc"]
                ],
            });
        }

        function anularExitMoney(id) {
            const fila = getRowById(dtExitMoneys, id);
            const costCenterName = fila.cost_center_name;
            const total = parseFloat(fila.total).toFixed(2);
            const date = fila.date;
            const supplier = fila.supplier_name;

            const messageHtml = `
                <div class="text-center" style="font-size: 15px;">
                    <p class="mb-2">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        <strong>Fecha:</strong> ${date}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-user text-primary me-2"></i>
                        <strong>Proveedor:</strong> ${supplier}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        <strong>Motivo:</strong> ${costCenterName}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-dollar-sign text-success me-2"></i>
                        <strong>Total:</strong> S/ ${total}
                    </p>
                </div>
            `;

            Swal.fire({
                title: '¿Desea anular este egreso?',
                html: messageHtml,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'No, cancelar',
                focusCancel: true,
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Anulando egreso...',
                        html: `
                    <div style="display:flex; align-items:center; justify-content:center; flex-direction:column;">
                        <i class="fa fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                        <p style="margin:0; font-weight:600;">Por favor, espere un momento</p>
                    </div>
                `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    try {
                        const res = await axios.delete(route('tenant.cajas.egresos.destroy', id), {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtExitMoneys.ajax.reload();
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }

                    } catch (error) {
                        toastr.error(error.response?.data?.message || error.message, 'ERROR EN LA PETICIÓN');
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelado',
                        text: 'La solicitud ha sido cancelada.',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }
    </script>
@endsection
