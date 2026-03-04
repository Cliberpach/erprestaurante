<script src="{{ asset('assets/libs/global/global.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatable.js') }}"></script>
<script src="{{ asset('assets/js/appSettings.js') }}"></script>

<script>
    window.lstSearchModules = @json($lst_search_modules);
    const baseUrl = @json($base);

    lstSearchModules.forEach((item) => {
        item.url = route(`${baseUrl}${item.url}`);
    })
</script>

<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
{{-- <script src="{{asset('assets/js/toast.js')}}"></script> --}}
<script src="{{ asset('assets/js/utils.js') }}"></script>

<script>
    window.sessionMessages = {
        success: @json(session('message_success')),
        error: @json(session('message_error'))
    };
</script>

@vite(['resources/js/global/main.js'])
@vite(['resources/js/utils/utils.js'])

@yield('js')
@stack('js-script')

<script>
    window.appMain = {
        tenantId: @json($tenant_id)
    }
</script>
@vite(['resources/js/alerts/main.js'])

