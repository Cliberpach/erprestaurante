@extends('layouts.template')

@section('title')
    Bienvenido a ComandaPro
@endsection

@section('content')

    {{-- ── Banner de bienvenida ── --}}
    <div class="card rounded-0 mb-0 border-0" style="height: auto;">
        <div class="card-body px-4 py-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <p class="text-muted small text-uppercase fw-semibold ls-1 mb-1">
                        <i class="fas fa-grip-dots me-2 opacity-50"></i>Panel de control
                    </p>
                    <h2 class="fw-light mb-1" style="font-size: clamp(1.5rem, 3vw, 2rem);">
                        Bienvenido(a), <span class="fw-bold">{{ Auth::user()->name ?? 'Administrador' }}</span>
                    </h2>
                    <p class="text-muted small fst-italic mb-3">
                        "{{ collect([
                            'El éxito de un restaurante se mide en sonrisas, no solo en platos.',
                            'Cada pedido es una oportunidad de superar expectativas.',
                            'La excelencia no es un acto, es un hábito diario.',
                            'Un equipo bien coordinado es la mejor receta.',
                            'El servicio excepcional comienza con una actitud excepcional.',
                            'Los detalles marcan la diferencia entre lo bueno y lo memorable.',
                            'Cada jornada es una nueva oportunidad de brillar.',
                            'La pasión por el servicio se nota en cada detalle.',
                            'Un cliente satisfecho es la mejor publicidad.',
                            'La consistencia es el ingrediente secreto del éxito.',
                            'Hoy es un buen día para dar lo mejor de ti.',
                            'El orden en cocina refleja el orden en el resultado.',
                            'Cada mesa es un escenario, cada servicio una actuación.',
                            'La hospitalidad es el corazón de todo restaurante.',
                            'Un gran equipo convierte lo ordinario en extraordinario.',
                            'La velocidad sin calidad no sirve; la calidad sin velocidad tampoco.',
                            'El respeto entre el equipo se traduce en respeto al cliente.',
                            'Pequeñas mejoras diarias generan grandes resultados.',
                            'Tu actitud de hoy define la experiencia del cliente mañana.',
                            'La dedicación de hoy construye la reputación de mañana.',
                        ])->random() }}"
                    </p>
                    <span class="badge rounded-pill d-inline-flex align-items-center gap-2 px-3 py-2"
                        style="background: rgba(34,197,94,0.12); color: #16a34a; border: 1px solid rgba(34,197,94,0.25); font-size: .7rem; font-weight: 500; letter-spacing: .05em;">
                        <span class="rounded-circle"
                            style="width:6px;height:6px;background:#22c55e;animation:blink 1.5s infinite;display:inline-block;"></span>
                        Sistema web de Gestión de Restaurantes
                    </span>
                </div>
                <div class="col-md-4 text-md-end d-none d-md-block">
                    <img src="{{ asset('assets/images/logo-full.svg') }}" alt="ComandaPro"
                        class="img-fluid opacity-90" style="max-height: 100px; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    {{-- ── Info del día ── --}}
    <div class="container-fluid px-4 pt-4">
        <div class="row g-3">

            {{-- Fecha y hora --}}
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm" style="height: auto;">
                    <div class="card-body d-flex align-items-center gap-3 py-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-label-primary"
                            style="width:52px;height:52px;">
                            <i class="fas fa-calendar-day text-primary fs-5"></i>
                        </div>
                        <div>
                            <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size:.65rem;letter-spacing:.1em;">
                                Hoy
                            </p>
                            <div class="fw-bold" style="font-size:1rem;" id="cp-fecha"></div>
                            <div class="text-muted small" id="cp-hora"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Usuario --}}
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-label-success"
                            style="width:52px;height:52px;">
                            <i class="fas fa-user text-success fs-5"></i>
                        </div>
                        <div>
                            <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size:.65rem;letter-spacing:.1em;">
                                Sesión activa
                            </p>
                            <div class="fw-bold" style="font-size:1rem;">{{ Auth::user()->name ?? '—' }}</div>
                            <div class="text-muted small">{{ Auth::user()->email ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Empresa / tenant --}}
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-label-warning"
                            style="width:52px;height:52px;">
                            <i class="fas fa-store text-warning fs-5"></i>
                        </div>
                        <div>
                            <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size:.65rem;letter-spacing:.1em;">
                                Empresa activa
                            </p>
                            <div class="fw-bold" style="font-size:1rem;">
                                ComandaPro
                            </div>
                            <div class="text-muted small">Sistema en línea</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Mensaje central ── --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-utensils fs-1 text-muted opacity-25 mb-3 d-block"></i>
                        <h5 class="fw-light mb-2">Usa el menú lateral para navegar</h5>
                        <p class="text-muted small mb-0">
                            Accede a cualquier módulo del sistema desde la barra de navegación izquierda.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: .2; }
        }
    </style>

@endsection

@section('js')
    <script src="{{ asset('assets/js/extended-ui-perfect-scrollbar.js') }}"></script>
    <script>
        // Fecha y hora en tiempo real
        const dias   = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
        const meses  = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

        function actualizarReloj() {
            const now = new Date();
            const fecha = `${dias[now.getDay()]}, ${now.getDate()} de ${meses[now.getMonth()]} de ${now.getFullYear()}`;
            const hora  = now.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('cp-fecha').textContent = fecha;
            document.getElementById('cp-hora').textContent  = hora;
        }

        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    </script>
@endsection
