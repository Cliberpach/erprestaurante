@push('js-head')
    @vite(['resources/js/libs/filepond.js'])
@endpush
@push('js-head')
    @vite(['resources/js/libs/lightgalery.js'])
@endpush

<div class="modal fade" id="mdlCharge" tabindex="-1" aria-labelledby="mdlChargeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="mdlChargeLabel">
                    <i class="fas fa-receipt text-primary"></i>
                    Cobrar orden
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3">
                @include('waiter_counter.counter.forms.form_charge')
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button class="btn btn-primary w-100" id="btn-charge" type="submit" form="formCharge">
                    <i class="fas fa-circle-check me-1"></i> Confirmar cobro
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    const paramsMdlCharge = {
        fpImgPay: null,
        order: null,
        lgQrPayment: null
    };

    function openMdlCharge(order) {
        paramsMdlCharge.order = order;
        paintOrderMdlCharge(order);
        $('#mdlCharge').modal('show');
    }

    function eventsMdlCharge() {
        loadSelectMdlCharge();
        loadFpMdlCharge();
        eChangeMdlCharge();
        eSubmitMdlCharge();

        document.getElementById('mdlCharge').addEventListener('hidden.bs.modal', function() {
            clearMdlCharge();
        });
    }

    function eSubmitMdlCharge() {
        document.querySelector('#formCharge').addEventListener('submit', (e) => {
            e.preventDefault();
            actionFormCharge(e.target);
        })
    }

    function eChangeMdlCharge() {
        window.paymentMethodSelect.on('change', () => actionPaymentMethodsChange());
    }

    function loadSelectMdlCharge() {
        window.paymentMethodSelect = loadSimpleSelect('payment_method');
    }

    function loadFpMdlCharge() {
        const inputLogo = document.querySelector('#inputVoucher');

        paramsMdlCharge.fpImgPay = FilePond.create(inputLogo, {
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
    }

    function paintOrderMdlCharge(order) {
        $('#spanOrderCode').text(order.order_code ?? '—');
        $('#spanTableName').text(order.table_name ?? '—');
        $('#spanTotal').text(parseFloat(order.total).toFixed(2));
    }

    async function actionPaymentMethodsChange() {
        toastr.clear();
        const imgQrMdlCharge = document.querySelector('#imgQrMdlCharge');
        const aImgQrMdlCharge = document.querySelector('.lg-qr-payment');
        const paymentMethodId = window.paymentMethodSelect.getValue();
        if (!paymentMethodId) {
            toastr.error('Debes seleccionar un método de pago!!!');
            imgQrMdlCharge.src = '';
            aImgQrMdlCharge.setAttribute('data-src', '');
            return;
        };

        mostrarAnimacion1();
        const res = await getBankAccountPayment(paymentMethodId);
        if (!res) {
            imgQrMdlCharge.src = '';
            aImgQrMdlCharge.setAttribute('data-src', '');
            ocultarAnimacion1();
            return;
        }

        const urlQrPayment = res.data.bank_account.qr_url;
        if (urlQrPayment) {
            imgQrMdlCharge.src = urlQrPayment;
            aImgQrMdlCharge.setAttribute('data-src', urlQrPayment);
            setImgQrPayment();
        }

        ocultarAnimacion1();
        $('#mdlQrPay').modal('show');
    }

    async function getBankAccountPayment(paymentMethodId) {
        try {
            const url = route('tenant.utils.getBackAccountPayment', {
                payment_method: paymentMethodId
            });
            const res = await axios.get(url);
            if (res.data.success) {
                toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
                return res;
            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                return null;
            }
        } catch (error) {
            toastr.error(error, 'Error en la petición obtener cuenta bancaria principal');
            return null;
        }
    }

    function setImgQrPayment() {
        if (paramsMdlCharge.lgQrPayment) {
            paramsMdlCharge.lgQrPayment.destroy();
        }

        const divQrPayment = document.querySelector('.div-qr-payment');
        paramsMdlCharge.lgQrPayment = lightGallery(divQrPayment, {
            selector: '.lg-qr-payment',
            plugins: [lgThumbnail, lgZoom],
            appendSubHtmlTo: '.lg-qr-payment',
            mobileSettings: {
                controls: true,
                showCloseIcon: true,
            }
        });
    }

    async function actionFormCharge(formCharge) {
        toastr.clear();

        const result = await Swal.fire({
            title: '¿Guardar pago?',
            text: "Confirmar",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });

        if (result.isConfirmed) {

            try {

                clearValidationErrors('msgError');

                Swal.fire({
                    title: 'Guardando pago...',
                    text: 'Procesando',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData(formCharge);
                formData.append('_method', 'PUT');

                const orderId = paramsMdlCharge.order.order_id;

                const res = await axios.post(route('tenant.mostrador_mesero.mostrador.addPay', orderId), formData);

                if (res.data.success) {
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    $('#mdlCharge').modal('hide');
                    Swal.close();
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } catch (error) {
                Swal.close();
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    paintValidationErrors(errors, 'error');
                    return;
                }
            }

        } else {

            Swal.fire({
                icon: 'info',
                title: 'Operación cancelada',
                text: 'No se realizaron acciones.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

        }
    }

    function clearMdlCharge() {
        paramsMdlCharge.fpImgPay.removeFiles();
    }
</script>
