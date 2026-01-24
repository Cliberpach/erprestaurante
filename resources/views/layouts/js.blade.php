<script src="{{ asset('assets/libs/global/global.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatable.js') }}"></script>
<script src="{{ asset('assets/js/appSettings.js') }}"></script>

<script>
    window.lstSearchModules = @json($lst_search_modules);
    const baseUrl = @json($base);

    lstSearchModules.forEach((item) => {
        item.url = route(`${baseUrl}${item.url}`);
    })
</script>

<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
{{-- <script src="{{asset('assets/js/toast.js')}}"></script> --}}
<script src="{{ asset('assets/js/utils.js') }}"></script>

<script>
    window.sessionMessages = {
        success: @json(session('message_success')),
        error: @json(session('message_error'))
    };
</script>
@vite(['resources/js/global/main.js'])

@yield('js')
@stack('js-script')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        notificationAudio = new Audio('/assets/sounds/bell-notification-337658.mp3');
        notificationAudio.volume = 0.5;

        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
        eventAlerts();
    })

    function eventAlerts() {

        document.body.addEventListener('click', enableAudio, {
            once: true
        });

        // Eventos de conexión
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ CONECTADO');
        });

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('❌ DESCONECTADO');
        });

        window.Echo.connector.pusher.connection.bind('error', (err) => {
            console.error('❌ ERROR:', err);
        });

        // Suscripción al canal
        const tenantId = @json($tenant_id);

        if (!tenantId) {
            console.warn('⚠️ Tenant no definido');
            return;
        }
        const channel = window.Echo.private(`alerts.${tenantId}`);

        channel.subscribed(() => {
            console.log('✅ SUSCRITO AL CANAL PRIVADO user.' + userId);
        });

        channel.error((error) => {
            console.error('❌ ERROR EN CANAL PRIVADO:', error);
        });

        channel.listen('.alert.created', (data) => {
            console.log('🔥 ALERTA RECIBIDA:', data);
            console.log('📋 Datos completos:', data.alert);
            emitAlert(data.alert);
        });

    }

    function emitAlert(alert) {
        // 1. Reproducir sonido
        playNotificationSound();

        showFacebookStyleToast(alert);

        // 2. Agregar al dropdown de notificaciones
        addNotificationToDropdown(alert);

        // 3. Incrementar contador
        updateNotificationBadge();

        // 4. Animar el icono de campana
        animateBellIcon();

        // NOTIFIED
        window.markAsNotified(alert.id);
    }

    function playNotificationSound() {
        const audio = new Audio('/assets/sounds/bell-notification-337658.mp3');
        audio.play().catch(e => console.log('No se pudo reproducir el sonido'));
    }

    function addNotificationToDropdown(alert) {
        const notificationList = document.querySelector('.list-group-hover');
        if (!notificationList) return;

        const notificationItem = document.createElement('li');
        notificationItem.className =
            'list-group-item d-flex justify-content-between align-items-center notification-new';

        notificationItem.innerHTML = `
        <div class="avatar avatar-xs bg-primary rounded-circle text-white">
            <i class="fi fi-rr-bell"></i>
        </div>
        <div class="me-auto ms-2">
            <h6 class="mb-0">Nueva notificación</h6>
            <small class="text-body d-block">${alert.content}</small>
            <small class="text-muted position-absolute end-0 top-0 me-3 mt-2">Ahora</small>
        </div>
    `;

        notificationList.insertBefore(notificationItem, notificationList.firstChild);

        setTimeout(() => {
            notificationItem.classList.add('notification-show');
        }, 10);

        setTimeout(() => {
            notificationItem.classList.remove('notification-new');
        }, 5000);
    }


    function updateNotificationBadge() {
        notificationCount++;

        const badge = document.querySelector('.dropdown-menu h6 .badge');
        if (badge) {
            badge.textContent = notificationCount;

            // Animar el badge
            badge.classList.add('badge-pulse');
            setTimeout(() => {
                badge.classList.remove('badge-pulse');
            }, 1000);
        }
    }

    function animateBellIcon() {
        const bellIcon = document.querySelector('.fi-rr-bell');
        if (bellIcon) {
            bellIcon.classList.add('bell-ring');
            setTimeout(() => {
                bellIcon.classList.remove('bell-ring');
            }, 1000);
        }
    }

    function redirectToObject(alert) {
        const routes = {
            'ORDEN_TRABAJO': '/ordenes-trabajo/',
            'COTIZACION': '/cotizaciones/',
            'VENTA': '/ventas/',
            'PRODUCCION': '/produccion/',
            'COMPRA': '/compras/',
        };

        const baseRoute = routes[alert.type_object];
        if (baseRoute) {
            window.location.href = baseRoute + alert.object_id;
        }
    }

    function showFacebookStyleToast(alert) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 8000,
            timerProgressBar: true,
            customClass: {
                popup: 'facebook-toast',
                title: 'facebook-toast-title',
                htmlContainer: 'facebook-toast-content',
                closeButton: 'facebook-toast-close'
            }
        });

        Toast.fire({
            icon: 'info',
            title: 'Nueva notificación',
            html: `
            <div class="toast-alert-body">
                <p class="mb-0 text-muted">${alert.content}</p>
            </div>
        `
        });
    }
    
    function getAlertIcon(typeObject) {
        const icons = {
            'ORDEN_TRABAJO': 'info',
            'COTIZACION': 'info',
            'VENTA': 'success',
            'PRODUCCION': 'warning',
            'COMPRA': 'info',
        };

        return icons[typeObject] || 'warning';
    }
</script>
