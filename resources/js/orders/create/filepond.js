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
        labelMaxFileSize: 'El tamaño máximo permitido es 4 MB'
    });

    setFpVoucher(fpVoucher)
}
