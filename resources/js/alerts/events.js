import { emitAlert, enableAudio, loadNotifications } from "./actions";
import { elements, tenantId } from "./state";

export function eventAlerts() {

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

    if (!tenantId) {
        console.warn('⚠️ Tenant no definido');
        return;
    }
    const channel = window.Echo.private(`alerts.${tenantId}`);

    channel.subscribed(() => {
        console.log('✅ SUSCRITO AL CANAL PRIVADO tenant.' + tenantId);
    });

    channel.error((error) => {
        console.error('❌ ERROR EN CANAL PRIVADO:', error);
    });

    channel.listen('.alert.created', (data) => {
        console.log('🔥 ALERTA RECIBIDA:', data);
        emitAlert(data);
    });

    elements.button?.addEventListener('click', () => {
        loadNotifications();
    });

}
