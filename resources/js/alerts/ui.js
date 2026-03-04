import { elements, notificationState } from "./state";

export function showFacebookStyleToast(alert) {
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

export function addNotificationToDropdown(alert) {
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

export function updateNotificationBadge(notificationCount) {
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

export function animateBellIcon() {
    const bellIcon = document.querySelector('.fi-rr-bell');
    if (bellIcon) {
        bellIcon.classList.add('bell-ring');
        setTimeout(() => {
            bellIcon.classList.remove('bell-ring');
        }, 1000);
    }
}

export function showLoadNotify() {
    elements.list.innerHTML = `
        <li class="list-group-item text-center text-muted">
            <div class="spinner-border spinner-border-sm mb-2" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <div>Cargando notificaciones...</div>
        </li>
    `;
}

export function updateNotificationUI(data, isAppend = false) {
    const {
        count,
        notifications
    } = data;

    // Actualizar contador
    elements.count.textContent = count;
    elements.countHeader.textContent = count;
    elements.count.style.display = count > 0 ? 'block' : 'none';

    // Si no es append, limpiar lista
    if (!isAppend) {
        elements.list.innerHTML = '';
    }

    if (notifications.length === 0 && !isAppend) {
        elements.list.innerHTML = `
            <li class="list-group-item text-center text-muted">
                <i class="fi fi-rr-bell fs-3 d-block mb-2"></i>
                No hay notificaciones
            </li>
        `;
        return;
    }

    // Agregar notificaciones
    notifications.forEach(notification => {
        const iconData = getIconForType(notification.type_object);

        const li = document.createElement('li');
        li.className =
            'list-group-item d-flex justify-content-between align-items-start position-relative notification-item';
        li.dataset.id = notification.id;
        li.dataset.type = notification.type_object;
        li.dataset.objectId = notification.object_id;

        li.innerHTML = `
            <div class="avatar avatar-xs ${iconData.bgClass} rounded-circle text-white">
                <i class="${iconData.icon}"></i>
            </div>
            <div class="flex-grow-1 ms-2">
                <h6 class="mb-1">${escapeHtml(notification.type)}</h6>
                <small class="text-body d-block">${escapeHtml(notification.content || '')}</small>
                <small class="text-muted">${escapeHtml(notification.time_ago)}</small>
            </div>
            <button class="btn btn-sm btn-link text-muted p-0 mark-as-read"
                    data-id="${notification.id}"
                    title="Marcar como leída">
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        elements.list.appendChild(li);
    });

    // Mostrar mensaje si no hay más
    if (!notificationState.hasMore && isAppend && notifications.length > 0) {
        const noMoreLi = document.createElement('li');
        noMoreLi.className = 'list-group-item text-center text-muted small';
        noMoreLi.innerHTML = `
            <i class="fi fi-rr-check-circle"></i> No hay más notificaciones
        `;
        elements.list.appendChild(noMoreLi);
    }
}

function getIconForType(typeObject) {
    const icons = {
        'ORDEN_TRABAJO': {
            icon: 'fi fi-rr-tool-box',
            bgClass: 'bg-primary'
        },
        'COTIZACION': {
            icon: 'fi fi-rr-calculator',
            bgClass: 'bg-info'
        },
        'VENTA': {
            icon: 'fi fi-rr-shopping-cart',
            bgClass: 'bg-success'
        },
        'PRODUCCION': {
            icon: 'fi fi-rr-settings',
            bgClass: 'bg-warning'
        },
        'COMPRA': {
            icon: 'fi fi-rr-shopping-bag',
            bgClass: 'bg-secondary'
        },
    };

    return icons[typeObject] || {
        icon: 'fi fi-rr-bell',
        bgClass: 'bg-dark'
    };
}

export function showErrorInList() {
    elements.list.innerHTML = `
        <li class="list-group-item text-center text-danger">
            <i class="fi fi-rr-cross-circle fs-3 d-block mb-2"></i>
            Error al cargar notificaciones
        </li>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
