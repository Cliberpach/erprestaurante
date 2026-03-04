import { getNotifications } from "./fetch";
import { notificationCount, notificationState, setAudioEnabled } from "./state";
import { addNotificationToDropdown, animateBellIcon, showErrorInList, showFacebookStyleToast, showLoadNotify, updateNotificationBadge, updateNotificationUI } from "./ui";

export function enableAudio() {
    setAudioEnabled(true);
}

export function emitAlert(alert) {
    // 1. Reproducir sonido
    playNotificationSound();

    showFacebookStyleToast(alert);

    // 2. Agregar al dropdown de notificaciones
    addNotificationToDropdown(alert);

    // 3. Incrementar contador
    updateNotificationBadge(notificationCount);

    // 4. Animar el icono de campana
    animateBellIcon();

    // NOTIFIED
    //window.markAsNotified(alert.id);
}

function playNotificationSound() {
    const audio = new Audio('/assets/sounds/bell-notification-337658.mp3');
    audio.play().catch(e => console.log('No se pudo reproducir el sonido'));
}


export async function loadNotifications() {

    notificationState.currentPage = 1;
    notificationState.hasMore = true;

    // Mostrar loading
    showLoadNotify();

    try {
        console.log('🔔 Cargando notificaciones del servidor...');
        const data = await getNotifications(1);
        console.log('✅ Notificaciones recibidas:', data);

        // Actualizar estado
        notificationState.hasMore = data.has_more;
        notificationState.totalCount = data.count;

        updateNotificationUI(data, false); // false = no es append
    } catch (error) {
        console.error('❌ Error al cargar notificaciones:', error);
        showErrorInList();
    }
}
