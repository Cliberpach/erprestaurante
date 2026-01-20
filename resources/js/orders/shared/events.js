import { actionDesktopQuery } from "./actions";
import { desktopQuery } from "./state";

export function eventsUtilChange() {
    desktopQuery.addEventListener('change', (e) => {
        actionDesktopQuery(e);
    });
}
