# 🚀 Guía de Despliegue en Producción (VPS Linux)

## Requisitos
- PHP 8.2+
- MySQL
- Redis
- Supervisor
- Composer

---

## 1. 📦 Instalar Redis en VPS

```bash
sudo apt update
sudo apt install redis-server

# Habilitar Redis para que inicie automáticamente
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Verificar que Redis está corriendo
redis-cli ping  # debe responder: PONG

# Instalar extensión PHP para Redis (importante: usar tu versión de PHP)
sudo apt install php8.2-redis

# Reiniciar PHP8.2-module
sudo systemctl restart apache2

# Verificar que la extensión está cargada
php -m | grep redis  # debe mostrar: redis
```

---

## 2. ⚙️ Configurar `.env` en Producción

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# ============ BASE DE DATOS ============
DB_CONNECTION=tenant
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

LANDLORD_HOST=127.0.0.1
LANDLORD_DATABASE=nombre_bd_landlord
LANDLORD_USERNAME=tu_usuario
LANDLORD_PASSWORD=tu_password
LANDLORD_PORT=3306

# ============ QUEUE CON REDIS ============
QUEUE_CONNECTION=redis

# ============ REDIS ============
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 3. 🔭 Instalar y Configurar Laravel Horizon

### Instalar Horizon (en desarrollo, luego subir con git)

```bash
# En desarrollo (Windows) — ignorar extensiones de Linux
composer require laravel/horizon --ignore-platform-reqs

# Publicar configuración
php artisan horizon:install
```

### En el VPS (ya con el código subido)

```bash
cd /var/www/erprestaurante
composer install --no-dev --optimize-autoloader
```

### Configurar `config/horizon.php`

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection'      => 'redis',
            'queue'           => ['invoices', 'invoice-retries', 'default'],
            'balance'         => 'auto',
            'minProcesses'    => 1,
            'maxProcesses'    => 5,
            'balanceMaxShift' => 1,
            'balanceCooldown' => 3,
            'tries'           => 1,
            'timeout'         => 120,
        ],
    ],

    'local' => [
        'supervisor-1' => [
            'connection'   => 'redis',
            'queue'        => ['invoices', 'invoice-retries', 'default'],
            'balance'      => 'simple',
            'processes'    => 3,
            'tries'        => 1,
        ],
    ],
],
```

### Configurar acceso al Dashboard de Horizon

En `app/Providers/HorizonServiceProvider.php`:

```php
protected function gate(): void
{
    Gate::define('viewHorizon', function ($user = null) {
        // Permitir solo a admins por email
        return in_array(optional($user)->email, [
            'admin@tudominio.com',
        ]);

        // O temporalmente para pruebas:
        // return true;
    });
}
```

---

## 4. 🛡️ Instalar y Configurar Supervisor

```bash
sudo apt install supervisor

# Crear configuración para Horizon
sudo nano /etc/supervisor/conf.d/horizon.conf
```

Contenido del archivo:

```ini
[program:horizon]
process_name=%(program_name)s
command=php /var/www/erprestaurante/artisan horizon
autostart=true
autorestart=true
user=root
redirect_stderr=true
stdout_logfile=/var/www/erprestaurante/storage/logs/horizon.log
stopwaitsecs=3600
```

```bash
# Activar Horizon con Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start horizon

# Verificar que está corriendo
sudo supervisorctl status
# Debe mostrar: horizon   RUNNING   pid XXXXX, uptime 0:00:XX
```

---

## 5. ⏰ Configurar Cron (Scheduler)

```bash
crontab -e

# Agregar esta línea:
* * * * * cd /var/www/erprestaurante && php artisan schedule:run >> /dev/null 2>&1

# Verificar que se guardó:
crontab -l
```

El scheduler ejecutará automáticamente:
- **1:00 AM** — Envío nocturno de boletas y facturas a SUNAT
- **Cada hora** — Reintentos de documentos fallidos

---

## 6. 🗄️ Migraciones en Producción

```bash
# Migrar tablas del landlord (jobs, failed_jobs)
php artisan migrate --path=database/migrations/landlord --database=landlord --force

# Migrar tablas de cada tenant (invoice_dispatch_logs, etc.)
php artisan tenants:migrate --force

# Sincronizar ventas históricas al sistema de dispatch
php artisan invoices:sync-pending
```

---

## 7. 🚀 Script de Deploy

Crea `deploy.sh` en la raíz del proyecto:

```bash
#!/bin/bash
echo "🚀 Iniciando deploy..."

git pull origin main

composer install --no-dev --optimize-autoloader

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --path=database/migrations/landlord --database=landlord --force
php artisan tenants:migrate --force

# Reiniciar Horizon para tomar cambios
php artisan horizon:terminate
sudo supervisorctl restart horizon

echo "✅ Deploy completado"
```

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 8. 🖥️ Acceder al Dashboard de Horizon

```
https://tudominio.com/horizon
```

### Qué ver en el Dashboard:

| Métrica | Descripción |
|---|---|
| **Status: Active** | Horizon corriendo correctamente |
| **Total Processes** | Workers activos escuchando colas |
| **Jobs Per Minute** | Rendimiento actual |
| **Failed Jobs** | Documentos que fallaron |
| **Completed Jobs** | Documentos enviados exitosamente |

---

## 9. 🔄 Comandos Útiles

```bash
# Ver estado de Horizon
sudo supervisorctl status

# Reiniciar Horizon (después de un deploy)
php artisan horizon:terminate
sudo supervisorctl restart horizon

# Ver logs de Horizon
tail -f /var/www/erprestaurante/storage/logs/horizon.log

# Ver logs de Laravel
tail -f /var/www/erprestaurante/storage/logs/laravel.log

# Ver jobs fallidos
php artisan queue:failed

# Reintentar todos los jobs fallidos
php artisan queue:retry all

# Ver estado de envíos de comprobantes
php artisan invoices:status

# Sincronizar ventas sin log
php artisan invoices:sync-pending
```

---

## 10. 📊 Flujo del Sistema de Envío Automático

```
1:00 AM (todos los días)
    └── Scheduler dispara automáticamente
            └── Por cada tenant registrado:
                    └── ProcessTenantInvoicesJob
                            └── Busca sales con status PENDIENTE
                                    └── SendInvoiceJob (por cada documento)
                                            ├── ACEPTADO  → status = ACEPTADO ✅
                                            ├── RECHAZADO → status = FALLIDO ❌ (no reintenta)
                                            └── PENDIENTE → reintenta con backoff:
                                                    15min → 1h → 3h → 8h → 24h

Cada hora:
    └── Reintenta documentos fallidos temporalmente
            └── Respeta expiración de 3 días (SUNAT)
```

---

## 11. ⚠️ Diferencias Dev vs Producción

| | Windows Dev | VPS Linux Producción |
|---|---|---|
| **Queue driver** | `database` | `redis` |
| **Workers** | `php artisan queue:work` | Horizon via Supervisor |
| **Scheduler** | `php artisan schedule:work` | Cron cada minuto |
| **Horizon UI** | ❌ No disponible | ✅ `tudominio.com/horizon` |
| **Instalar Horizon** | `--ignore-platform-reqs` | Normal |
