# 🚀 Guía de Despliegue en Producción (VPS Linux)

## Requisitos
- PHP 8.2+
- MySQL
- Redis
- Supervisor (para Horizon)
- Composer
- Node.js v18+ via NVM (para Soketi)
- PM2 (para gestionar Soketi)
- Soketi v0.39+ (WebSockets)

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

# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm

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
# IMPORTANTE: este proyecto usa tenants:artisan, no tenants:migrate
php artisan tenants:artisan "migrate --path=database/migrations/tenant --force"

# Sincronizar ventas históricas al sistema de dispatch
php artisan invoices:sync-pending
```

---

## 7. 🔌 Instalar Soketi (WebSockets) con NVM + PM2

Soketi reemplaza a Pusher Cloud — corre en tu propio servidor.
Se gestiona con **PM2** (no Supervisor) porque es Node.js.

### 7.1 Instalar NVM y Node.js

```bash
# Cambiar al usuario deploy (NO usar root para esto)
su - deploy

# Instalar NVM
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash

# Recargar el perfil
source ~/.bashrc

# Verificar NVM
nvm --version

# Instalar Node.js v18 (LTS recomendado para Soketi)
su - deploy
nvm install 18
nvm use 18
nvm alias default 18

# Verificar
node -v   # debe mostrar v18.x.x
npm -v
```

### 7.2 Instalar PM2 y Soketi

```bash
# Estando como usuario deploy
su - deploy
npm install -g pm2
npm install -g @soketi/soketi

# Verificar instalaciones
pm2 --version      # ej: 6.0.14
soketi --version   # ej: 0.39.7
```

### 7.3 Iniciar Soketi con PM2

```bash
# Estando como usuario deploy
pm2 start /home/deploy/.nvm/versions/node/v18.20.8/bin/soketi \
    --name soketi \
    -- start --host 0.0.0.0 --port 6001 --log-level debug

# Verificar que está corriendo
pm2 list
# Debe mostrar: soketi | online

# Ver detalles
pm2 show soketi
```

### 7.4 Configurar PM2 para auto-inicio al reboot

```bash
# Estando como usuario deploy — genera el comando de startup
pm2 startup
# Te mostrará un comando para ejecutar como root, algo como:
# sudo env PATH=$PATH:/home/deploy/.nvm/versions/node/v18.20.8/bin \
#   /home/deploy/.nvm/versions/node/v18.20.8/lib/node_modules/pm2/bin/pm2 \
#   startup systemd -u deploy --hp /home/deploy

# Copia y ejecuta ESE comando como root:
exit  # salir de deploy
sudo env PATH=... (el comando que te dio pm2 startup)

# Volver a deploy y guardar el estado actual
su - deploy
pm2 save
```

#Variables config pm2 linux
Crear archivo `ecosystem.config.cjs` en la raíz del proyecto:
```javascript
module.exports = {
  apps: [
    {
      name: "soketi",
      script: "soketi",
      args: "start --host 0.0.0.0 --port 6001 --log-level debug",
      watch: false,
      autorestart: true,
      max_memory_restart: "512M",
      env: {
        NODE_ENV: "production",
        SOKETI_DEFAULT_APP_ID: "1",
        SOKETI_DEFAULT_APP_KEY: "app-key",
        SOKETI_DEFAULT_APP_SECRET: "app-secret",
        SOKETI_DEFAULT_APP_ENABLE_CLIENT_MESSAGES: "true",
        SOKETI_DEBUG: "true"
      }
    }
  ]
};
```

###Variables config pm2 windows 
```
$env:SOKETI_DEBUG = "true"
$env:SOKETI_HOST = "127.0.0.1" 
$env:SOKETI_PORT = "6001"
$env:SOKETI_DEFAULT_APP_ID = "1"
$env:SOKETI_DEFAULT_APP_KEY = "app-key"
$env:SOKETI_DEFAULT_APP_SECRET = "app-secret"
$env:SOKETI_DEFAULT_APP_ENABLE_CLIENT_MESSAGES = "true"
```

luego iniciar con pm2:
cd /var/www/erprestaurante
pm2 start ecosystem.config.cjs
```

### 7.5 Comandos útiles de PM2

```bash
# Estando como usuario deploy:
pm2 list                    # ver todos los procesos
pm2 show soketi             # detalles de soketi
pm2 logs soketi             # ver logs en tiempo real
pm2 logs soketi --lines 100 # últimas 100 líneas
pm2 restart soketi          # reiniciar soketi
pm2 stop soketi             # detener soketi
pm2 monit                   # monitor de CPU y memoria
```

### 7.6 Configurar `.env` para Soketi

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=1
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

> ⚠️ **Nota:** Soketi NO se gestiona con Supervisor — usar PM2 porque es Node.js.
> Supervisor solo gestiona procesos PHP (Horizon).

---

## 8. 🚀 Script de Deploy

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

# Migrar landlord
php artisan migrate --path=database/migrations/landlord --database=landlord --force

# Migrar todos los tenants
php artisan tenants:artisan "migrate --path=database/migrations/tenant --force"

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

## 9. 🖥️ Acceder al Dashboard de Horizon

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

## 10. 🔄 Comandos Útiles

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

## 11. 📊 Flujo del Sistema de Envío Automático

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

## 12. ⚠️ Diferencias Dev vs Producción

| | Windows Dev | VPS Linux Producción |
|---|---|---|
| **Queue driver** | `database` | `redis` |
| **Workers** | `php artisan queue:work` | Horizon via Supervisor |
| **Scheduler** | `php artisan schedule:work` | Cron cada minuto |
| **Horizon UI** | ❌ No disponible | ✅ `tudominio.com/horizon` |
| **Instalar Horizon** | `--ignore-platform-reqs` | Normal |
| **WebSockets** | Soketi local | Soketi via PM2 |

---

## 13. 🏗️ Stack Completo en Producción

| Servicio | Gestor | Puerto |
|---|---|---|
| **Apache2** | systemd | 80, 443 |
| **MySQL** | systemd | 3306 |
| **Redis** | systemd | 6379 |
| **Horizon** (Laravel Queue) | Supervisor | - |
| **Soketi** (WebSockets) | PM2 (usuario deploy) | 6001 |
| **Scheduler** | Cron | - |

```bash
# Ver estado de todos los servicios
sudo supervisorctl status          # Horizon
su - deploy -c "pm2 list"          # Soketi
sudo systemctl status redis        # Redis
sudo systemctl status apache2      # Apache
```
