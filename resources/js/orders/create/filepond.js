import { setFpVoucher } from "../shared/state";

export function loadFilePond() {
    const inputLogo = document.querySelector('#voucher');

    const fpVoucher = FilePond.create(inputLogo, {
        allowImagePreview: true,
        imagePreviewHeight: 120,
        imageCropAspectRatio: '1:1',
        styleLayout: 'compact',
        stylePanelAspectRatio: 0.5,
        storeAsFile: true,

        allowFileTypeValidation: true,
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],

        allowFileSizeValidation: true,
        maxFileSize: '4MB',

        labelFileTypeNotAllowed: 'Solo se permiten imágenes PNG,JPG,JPEG',
        fileValidateTypeLabelExpectedTypes: 'Formatos válidos: PNG, JPG, JPEG',
        labelMaxFileSizeExceeded: 'El archivo es demasiado grande',
        labelMaxFileSize: 'El tamaño máximo permitido es 4 MB',
        labelIdle: `
            Arrastra y suelta el voucher o <span class="filepond--label-action">Examinar</span><br>
            <small>Máx 4MB • JPG, PNG, JPEG</small>
        `,
    });

    setFpVoucher(fpVoucher)
}
