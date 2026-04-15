<form action="" id="frmConfiguration">
    @foreach ($configuration as $item)
        <div class="row mb-4">

            @if ($item->id == 1)
                <div class="col-lg-6 col-md-6 col-sm-6 d-flex align-items-center">
                    <label for="configuration_{{ $item->id }}" style="font-weight: bold;">
                        {{ $item->description }}
                    </label>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <x-toggle-switch-1 id="configuration_{{ $item->id }}" name="configuration_{{ $item->id }}"
                        :checked="$item->property == 1" />
                    <p class="configuration_{{ $item->id }}_error msgError"></p>
                </div>
            @endif

            @if ($item->id == 2)
                <div class="col-lg-6 col-md-6 col-sm-6 d-flex align-items-center">
                    <label for="configuration_{{ $item->id }}" style="font-weight: bold;">
                        {{ $item->description }}
                    </label>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="d-flex align-items-center gap-2">

                        {{-- Switch --}}
                        <x-toggle-switch-1 id="configuration_{{ $item->id }}"
                            name="configuration_{{ $item->id }}" :checked="$item->property == 1" />

                    </div>

                    <p class="configuration_{{ $item->id }}_error msgError"></p>
                </div>
            @endif

            @if ($item->id == 3)
                <div class="col-lg-6 col-md-6 col-sm-6 d-flex align-items-center">
                    <label for="configuration_{{ $item->id }}" style="font-weight: bold;">
                        {{ $item->description }}
                    </label>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="d-flex align-items-center gap-2">

                        {{-- Switch --}}
                        <x-toggle-switch-1 id="configuration_{{ $item->id }}"
                            name="configuration_{{ $item->id }}" :checked="$item->property == 1" />

                    </div>

                    <p class="configuration_{{ $item->id }}_error msgError"></p>
                </div>
            @endif

            @if ($item->id == 4)
                <div class="col-lg-6 col-md-6 col-sm-6 d-flex align-items-center">
                    <label for="configuration_{{ $item->id }}" style="font-weight: bold;">
                        {{ $item->description }}
                    </label>
                </div>

                {{-- Input group password --}}
                <div class="input-group" style="max-width: 240px;">
                    <input value="{{ $item->property }}" type="password" class="form-control input-fill"
                        id="configuration_{{ $item->id }}" name="configuration_{{ $item->id }}"
                        placeholder="Contraseña">

                    <span class="input-group-text bg-light toggle-password" style="cursor: pointer;"
                        data-target="configuration_{{ $item->id }}">
                        <i class="fas fa-lock text-warning"></i>
                    </span>
                </div>
                <p class="configuration_{{ $item->id }}_error msgError"></p>
            @endif


        </div>
    @endforeach
</form>

@push('js-script')
    <script>
        document.addEventListener('click', function(e) {
            const toggle = e.target.closest('.toggle-password');
            if (!toggle) return;

            const input = document.getElementById(toggle.dataset.target);
            const icon = toggle.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-lock');
                icon.classList.add('fa-lock-open');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-lock-open');
                icon.classList.add('fa-lock');
            }
        });
    </script>
@endpush
