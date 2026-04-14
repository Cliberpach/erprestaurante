import { actionFormPassword } from "./action";

export function eventsMdlPassword() {
    document.querySelector('#form-password').addEventListener('submit',(e)=>{
        actionFormPassword(e);
    })
}
