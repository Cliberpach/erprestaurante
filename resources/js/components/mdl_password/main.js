import { eventsMdlPassword } from "./events";
import { getConfig, setConfig } from "./state";
import { paintTitleMdlPass } from "./ui";

document.addEventListener('DOMContentLoaded', () => {
    eventsMdlPassword();
})

export function loadMdlPassword(config) {
    setConfig(config);
    paintTitleMdlPass(getConfig());
}
