import { bindHeaderCheckbox, bindRowEvents, isSelected } from "./actions";
import { eventsDtAlerts } from "./events";
import { routes } from "./route";
import { setDtAlerts } from "./state";

export function loadDtAlerts() {
    const url = routes.getAlertsCash;

    const instance = new DataTable('.dt-alerts', {
        serverSide: true,
        processing: true,
        info: false,
        pagingType: "simple_numbers",
        lengthChange: false,
        paging: false,
        ajax: {
            url: url,
            type: 'GET',
            data: function (d) {
                d.categoria_id = $('#categoria').val();
                d.marca_id = $('#marca').val();
            }
        },
        scrollY: '320px',      // altura del body de la tabla
        scrollCollapse: true,  // se encoge si hay pocos registros
        scrollX: false,
        columns: [
            {
                data: null,
                name: 'select',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {
                    const checked = isSelected(row.id) ? 'checked' : '';
                    return `<input type="checkbox" class="form-check-input notif-chk" ${checked}>`;
                }
            },
            {
                data: 'content',
                name: 'a.content',
                className: 'align-middle',
                render: function (data, type, row) {
                    return `
                        <div class="notif-title">${data ?? '—'}</div>
                        <div class="notif-sub text-muted">${row.subtitle ?? ''}</div>
                    `;
                }
            },
            {
                data: 'created_at',
                name: 'a.created_at',
                className: 'align-middle text-center',
                render: function (data) {
                    return data ?? '—';
                }
            }
        ],
        pageLength: 25,
        lengthChange: false,
        dom: '<"row mb-3"<"col-12"f>>t<"row"<"col-6"i><"col-6"p>>',
        language: {
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            emptyTable: "No hay datos disponibles en la tabla",
            aria: {
                sortAscending: ": activar para ordenar ascendente",
                sortDescending: ": activar para ordenar descendente"
            }
        },
        // Tras cada redibujado re-añadimos clase notif-row y re-bindeamos
        createdRow: function (row) {
            row.classList.add('notif-row');
        },
        drawCallback: function () {
            bindRowEvents();
            bindHeaderCheckbox();
        }
    });

    setDtAlerts(instance);
    eventsDtAlerts();
}
