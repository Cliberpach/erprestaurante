<?php

namespace App\Http\Services\Landlord\Maintenance\Company;

use Illuminate\Validation\ValidationException;

class CompanyValidation
{
    public function validationStore(array $data)
    {
        if (isset($data['certificate'])) {
            $certificateFile        =   $data['certificate'];
            $certificate_password   =   isset($data['certificate_password']) ?? null;
            $extension              =   strtolower($certificateFile->getClientOriginalExtension());

            $this->validationCertificate($extension, $certificate_password);
        }
    }

    public function validationCertificate(string $extension, $certificate_password)
    {
        if (!in_array($extension, ['pem', 'p12'])) {
            throw ValidationException::withMessages([
                'certificate' => [
                    'El certificado debe tener extensión .pem o .p12.'
                ]
            ]);
        }

        if ($extension === 'p12' && !$certificate_password) {
            throw ValidationException::withMessages([
                'certificate_password' => [
                    'La contraseña del certificado es obligatoria cuando se sube un archivo P12.'
                ]
            ]);
        }
    }
}
