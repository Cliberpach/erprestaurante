import { eventAlerts } from "./events";
import { notificationAudio } from "./state";

document.addEventListener('DOMContentLoaded', () => {
    notificationAudio.volume = 0.5;

    if ("Notification" in window && Notification.permission === "default") {
        Notification.requestPermission();
    }
    eventAlerts();
})
