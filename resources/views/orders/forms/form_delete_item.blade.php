<form action="" id="form-delete-item">
    <div class="row">
        <div class="col-12">
            <label for="password" class="form-label fw-semibold">
                Contraseña
            </label>

            <div class="input-group">
                <span class="input-group-text bg-light" id="toggle-password" style="cursor: pointer;">
                    <i class="fas fa-lock text-warning"></i>
                </span>

                <input type="password" maxlength="30" id="password" name="password"
                    class="form-control" placeholder="Ingrese contraseña" required>
            </div>
        </div>
    </div>
</form>
@push('styles')
    <style>
        #toggle-password {
            transition: background-color 0.2s ease;
        }

        #toggle-password i {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        #toggle-password:hover {
            background-color: #fff3cd;
        }

        #toggle-password:hover i {
            transform: scale(1.15);
            color: #d39e00;
        }
    </style>
@endpush
@push('js-script')
    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');

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
