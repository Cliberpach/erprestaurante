import { routes } from "./routes";
import { config, setStatePassword } from "./state";

export function openMdlPassword() {
    const modalEl = document.getElementById('mdlPassword');

    if (!modalEl) {
        console.warn('Modal mdlPassword no encontrado');
        return;
    }

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

export function closeMdlPassword() {
    const modalEl = document.getElementById('mdlPassword');
    bootstrap.Modal.getInstance(modalEl).hide();
}

export async function actionFormPassword(e) {
    e.preventDefault();

    const confirm = await Swal.fire({
        title: '¿Confirmar acción?',
        text: 'Se validará tu contraseña para continuar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, validar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    });

    if (!confirm.isConfirmed) return;

    try {

        // 🔄 Loading
        Swal.fire({
            title: 'Validando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(e.target);
        const res = await axios.post(routes.validationPassword, formData);

        Swal.close(); // cerrar loading

        if (res.data.success) {

            setStatePassword(res.data.success);
            config.onSuccess();

            // ✅ Éxito
            await Swal.fire({
                icon: 'success',
                title: 'Correcto',
                text: 'Contraseña válida'
            });

            closeMdlPassword();

        } else {

            setStatePassword(res.data.success);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Contraseña incorrecta'
            });

        }

    } catch (error) {

        Swal.close();

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un problema al validar'
        });

        console.error(error);
    }
}
