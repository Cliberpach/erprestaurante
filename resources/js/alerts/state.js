export const tenantId = appMain.tenantId;
export let audioEnabled = false;
export let notificationCount = 0;
export const notificationAudio = new Audio('/assets/sounds/bell-notification-337658.mp3');
export const elements = {
    button: document.getElementById('notificationButton'),
    count: document.getElementById('notificationCount'),
    countHeader: document.getElementById('notificationCountHeader'),
    list: document.getElementById('notificationList'),
    markAllBtn: document.getElementById('markAllAsRead'),
    scrollContainer: null
};
export let notificationState = {
    currentPage: 1,
    isLoading: false,
    hasMore: true,
    totalCount: 0
};

export function setAudioEnabled(instance) {
    audioEnabled = instance;
}
