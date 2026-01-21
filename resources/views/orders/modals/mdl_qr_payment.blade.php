<div class="modal fade" id="mdlQrPay" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">QR DE PAGO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12 d-flex justify-content-center align-items-center">
                        <img src="" alt="" id="img-qr-payment" style="height:300px;object-fit: cover;">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

            </div>
        </div>
    </div>
</div>

<style>
    #tbl_dishes_wrapper .dt-search {
        text-align: start;
    }
</style>

@push('js-script')
    <script>


        function eventsMdlQrPay() {

        }

        function openMdlQrPayment() {
            $('#mdlQrPay').modal('show');
        }


        function clearMdlQrPayment() {
            document.querySelector('#img-qr-payment').src = '';
        }
    </script>
@endpush
