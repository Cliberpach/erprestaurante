import { loadSimpleSelect } from "./selects/simple/main";

export function isDesktop() {
    return window.innerWidth >= 992;
}

window.isDesktop        =   isDesktop;
window.loadSimpleSelect =   loadSimpleSelect;
