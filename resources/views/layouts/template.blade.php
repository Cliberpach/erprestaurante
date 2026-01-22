<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head')
</head>

<body>

    <div class="row">
        <div class="col-12">
            @include('utils.spinners.spinner_1')
        </div>
    </div>

    <button class="theme-toggle d-none">
        <span class="theme-icon">🌓</span>
        <span id="theme-text">Modo Oscuro</span>
    </button>

    <!-- Overlay del loader -->
    @include('layouts.loaders.loader1')

    <div class="page-layout">

        <!-- begin::GXON Page Header -->
        <header class="app-header">
            @include('layouts.body.header')
        </header>
        <!-- end::GXON Page Header -->

        @include('layouts.body.mdl_search')

        <!-- begin::GXON Sidebar Menu -->
        <aside class="app-menubar" id="appMenubar">
            @include('layouts.body.aside')
        </aside>
        <!-- end::GXON Sidebar Menu -->

        <!-- begin::GXON Sidebar right -->
        {{-- <div class="app-sidebar-end">
            @include('layouts.body.app-sidebar-end')
        </div> --}}
        <!-- end::GXON Sidebar right -->

        <main class="app-wrapper">
            @include('layouts.body.main')
        </main>

        <!-- begin::GXON Footer -->
        <footer class="footer-wrapper bg-body">
            @include('layouts.body.footer')
        </footer>
        <!-- end::GXON Footer -->

    </div>

    <!-- begin::GXON Page Scripts -->
    @include('layouts.js')
    <!-- end::GXON Page Scripts -->
</body>

</html>
