# 🚀 Guía de Despliegue en Producción (VPS Linux)

## 🖥️ Entorno Probado
- **Ubuntu 24.04.3 LTS (Noble)**
- **PHP 8.2**
- **MySQL 8.0**
- **Apache2** (mod_php, sin php-fpm)
- **Redis 7**
- **Node.js v18.20.8** via NVM
- **Soketi v0.39.7**
- **PM2 v6.0.14**
- **Composer 2+**
- **Supervisor**

---

## 📋 PARTE 1 — Instalación desde Cero en Ubuntu 24.04

### 1.1 🔧 Actualizar el sistema

```bash
sudo apt update
sudo apt upgrade -y
```

---

### 1.2 🌐 Instalar Apache2

```bash
sudo apt install apache2

# Habilitar mod_rewrite (necesario para Laravel)
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### 1.3 🔥 Configurar Firewall (UFW)

```bash
sudo ufw enable
# ⚠️ Preguntará: "Command may disrupt existing ssh connections. Proceed with operation (y|n)?"
# Responder: y

# Permitir Apache (puertos 80 y 443)
sudo ufw allow in "Apache Full"

# ⚠️ MUY IMPORTANTE: permitir SSH para no perder acceso al servidor
sudo ufw allow 22/tcp

# Permitir Soketi WebSockets
sudo ufw allow 6001/tcp

# Verificar reglas activas
sudo ufw status
```

---

### 1.4 🗄️ Instalar MySQL

```bash
sudo apt install mysql-server

# Primer intento (puede dar error de contraseña, es normal)
sudo mysql_secure_installation
# Si pide contraseña y no tienes → sal con Ctrl+C

# Entrar a MySQL sin contraseña
sudo mysql

# Dentro de MySQL: establecer contraseña root
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'TU_PASSWORD_AQUI';
exit;

# Ahora sí ejecutar la segurización completa
sudo mysql_secure_installation
# → Ingresar la contraseña que pusiste arriba
# → Cuando pregunte si cambiar contraseña: N (ya la configuraste)
# → El resto: Y, Y, Y, Y, Y

sudo service apache2 restart
```

---

### 1.5 🐘 Instalar PHP 8.2

```bash
# Agregar repositorio de PHP 8.2
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Instalar PHP 8.2 con todas las extensiones necesarias para Laravel
sudo apt install php8.2 libapache2-mod-php8.2 php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl

# Verificar versión instalada
php -v

# Habilitar mbstring
sudo phpenmod mbstring
sudo systemctl restart apache2
```

---

### 1.6 📦 Instalar Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verificar
composer --version
```

---

### 1.7 🐙 Instalar Git y Clonar Repositorio

```bash
sudo apt-get install git

# Ir a la carpeta web
cd /var/www

# Clonar el repositorio
git clone https://github.com/tu-usuario/erprestaurante.git

# Entrar al proyecto
cd erprestaurante
```

---

### 1.8 🔐 Configurar Permisos

```bash
# Dar permisos de escritura a storage (necesario para Laravel)
sudo chown -R www-data: storage
sudo chmod -R 777 storage
sudo chmod -R 777 bootstrap/cache
```

---

### 1.9 🌍 Configurar Apache VirtualHost

```bash
sudo nano /etc/apache2/sites-enabled/000-default.conf
```

Contenido del archivo:

```apache
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/erprestaurante/public

    <Directory /var/www/erprestaurante/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/erprestaurante_error.log
    CustomLog ${APACHE_LOG_DIR}/erprestaurante_access.log combined
</VirtualHost>
```

```bash
sudo service apache2 restart
```

---

### 1.10 ⚙️ Configurar Laravel

```bash
cd /var/www/erprestaurante

# Instalar dependencias de producción
composer install --no-dev --optimize-autoloader

# Copiar y editar el .env
cp .env.example .env
nano .env  # configurar BD, Redis, Soketi, etc.

# Generar clave de aplicación
php artisan key:generate

# Enlace simbólico de storage
php artisan storage:link

# Migrar BD landlord (tablas globales: jobs, failed_jobs, tenants)
php artisan migrate --path=database/migrations/landlord --database=landlord --force

# Migrar BD de cada tenant (invoice_dispatch_logs, sales, etc.)
# ⚠️ Este proyecto usa tenants:artisan, no tenants:migrate
php artisan tenants:artisan "migrate --path=database/migrations/tenant --force"

# Sincronizar ventas históricas (solo la primera vez)
php artisan invoices:sync-pending
```

---

### 1.11 ⚙️ Configurar `.env` en Producción

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com
APP_TIMEZONE=America/Lima

# ============ BASE DE DATOS ============
DB_CONNECTION=tenant
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=root
DB_PASSWORD=tu_password

LANDLORD_HOST=127.0.0.1
LANDLORD_DATABASE=nombre_bd_landlord
LANDLORD_USERNAME=root
LANDLORD_PASSWORD=tu_password
LANDLORD_PORT=3306

# ============ QUEUE CON REDIS ============
QUEUE_CONNECTION=redis

# ============ REDIS ============
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ============ WEBSOCKETS (SOKETI) ============
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=1
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

---

## 📋 PARTE 2 — Instalación de Servicios

### 2.1 📦 Instalar Redis

```bash
sudo apt install redis-server

# Habilitar inicio automático
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Verificar que responde
redis-cli ping  # debe responder: PONG

# Instalar extensión PHP para Redis
# ⚠️ NO usar: sudo apt install php-redis (instalaría PHP 8.5 automáticamente)
# Usar específicamente para PHP 8.2:
sudo apt install php8.2-redis

# Reiniciar Apache (este proyecto usa mod_php, NO php-fpm)
sudo systemctl restart apache2

# Verificar que la extensión está cargada
php -m | grep redis  # debe mostrar: redis
```

---

### 2.2 🔭 Instalar Laravel Horizon

> ⚠️ Horizon se instala en **DESARROLLO (Windows)**, no directamente en el VPS.
> Luego se sube con git al VPS.

```bash
# En Windows (dev) — ignorar extensiones que solo existen en Linux:
composer require laravel/horizon --ignore-platform-reqs
php artisan horizon:install
# Subir cambios con: git push
```

En el VPS solo ejecutar:

```bash
composer install --no-dev --optimize-autoloader
```

Configurar `config/horizon.php`:

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

Configurar acceso al Dashboard en `app/Providers/HorizonServiceProvider.php`:

```php
protected function gate(): void
{
    Gate::define('viewHorizon', function ($user = null) {
        return in_array(optional($user)->email, [
            'admin@tudominio.com',
        ]);
        // Para pruebas temporales usar: return true;
    });
}
```

---

### 2.3 🛡️ Instalar Supervisor (para Horizon)

```bash
sudo apt install supervisor

# Crear configuración de Horizon
sudo nano /etc/supervisor/conf.d/horizon.conf
```

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
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start horizon

# Verificar
sudo supervisorctl status
# Debe mostrar: horizon   RUNNING   pid XXXXX, uptime 0:00:XX
```

Acceder al dashboard:
```
https://tudominio.com/horizon
```

---

### 2.4 ⏰ Configurar Cron (Scheduler Laravel)

```bash
crontab -e

# Agregar al final del archivo:
* * * * * cd /var/www/erprestaurante && php artisan schedule:run >> /dev/null 2>&1

# Verificar que se guardó
crontab -l
```

El scheduler ejecutará automáticamente:
- **1:00 AM** — Envío nocturno de boletas/facturas a SUNAT
- **Cada hora** — Reintentos de documentos fallidos

---

### 2.5 🔌 Instalar Soketi (WebSockets) con NVM + PM2

> ⚠️ Soketi se gestiona con **PM2** (no Supervisor) porque es Node.js.
> Se ejecuta como usuario **deploy**, NO como root.

#### Crear usuario deploy (si no existe)

```bash
# Como root:
adduser deploy
usermod -aG sudo deploy
```

#### Instalar NVM y Node.js v18

```bash
# Cambiar al usuario deploy
su - deploy

# Instalar NVM
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash

# Recargar perfil
source ~/.bashrc

# Verificar NVM
nvm --version

# Instalar Node.js v18 (versión probada: v18.20.8)
nvm install 18
nvm use 18
nvm alias default 18

# Verificar
node -v  # debe mostrar v18.x.x
npm -v
```

#### Instalar PM2 y Soketi

```bash
# Estando como usuario deploy
npm install -g pm2
npm install -g @soketi/soketi

# Verificar
pm2 --version    # ej: 6.0.14
soketi --version # ej: 0.39.7
```

#### Crear `ecosystem.config.cjs` en la raíz del proyecto

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

#### Iniciar Soketi con PM2

```bash
# Estando como usuario deploy
cd /var/www/erprestaurante
pm2 start ecosystem.config.cjs

# Verificar
pm2 list         # debe mostrar: soketi | online
pm2 show soketi  # detalles completos
```

#### Configurar PM2 para auto-inicio al reboot

```bash
# Estando como usuario deploy
pm2 startup
# ⚠️ Generará un comando para ejecutar como root, ejemplo:
# sudo env PATH=$PATH:/home/deploy/.nvm/versions/node/v18.20.8/bin \
#   /home/deploy/.nvm/versions/node/v18.20.8/lib/node_modules/pm2/bin/pm2 \
#   startup systemd -u deploy --hp /home/deploy

# Salir a root y ejecutar ESE comando exacto:
exit
sudo env PATH=... (pegar el comando que generó pm2 startup)

# Volver a deploy y guardar el estado
su - deploy
pm2 save
```

#### Comandos útiles de PM2

```bash
# Como usuario deploy:
pm2 list                     # ver todos los procesos
pm2 show soketi              # detalles de soketi
pm2 logs soketi              # logs en tiempo real
pm2 logs soketi --lines 100  # últimas 100 líneas
pm2 restart soketi           # reiniciar
pm2 stop soketi              # detener
pm2 monit                    # monitor CPU/memoria en tiempo real
```

---

## 📋 PARTE 3 — Mantenimiento y Operación

### 3.1 🚀 Script de Deploy (para actualizaciones)

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

# Reiniciar Horizon gracefully
php artisan horizon:terminate
sudo supervisorctl restart horizon

echo "✅ Deploy completado"
```

```bash
chmod +x deploy.sh
./deploy.sh
```

---

### 3.2 🔄 Comandos de Mantenimiento

```bash
# ===== HORIZON =====
sudo supervisorctl status                  # ver estado
php artisan horizon:terminate              # detener gracefully
sudo supervisorctl restart horizon         # reiniciar
tail -f storage/logs/horizon.log           # ver logs en tiempo real

# ===== SOKETI =====
su - deploy -c "pm2 list"                  # ver estado
su - deploy -c "pm2 restart soketi"        # reiniciar
su - deploy -c "pm2 logs soketi"           # ver logs

# ===== LARAVEL =====
tail -f storage/logs/laravel.log           # ver logs
php artisan queue:failed                   # ver jobs fallidos
php artisan queue:retry all                # reintentar todos los fallidos
php artisan invoices:sync-pending          # sincronizar ventas sin log

# ===== SERVICIOS =====
sudo supervisorctl status                  # Horizon
sudo systemctl status redis                # Redis
sudo systemctl status apache2              # Apache
redis-cli ping                             # verificar Redis (PONG = OK)
```

---

### 3.3 📊 Flujo del Sistema de Envío Automático SUNAT

```
1:00 AM (todos los días)
    └── Scheduler dispara automáticamente
            └── Por cada tenant registrado:
                    └── ProcessTenantInvoicesJob
                            └── Busca sales con status PENDIENTE
                                    └── SendInvoiceJob (por cada documento)
                                            ├── ACEPTADO  → status = ACEPTADO ✅
                                            ├── RECHAZADO → status = FALLIDO ❌ (no reintenta)
                                            └── PENDIENTE → reintenta con backoff exponencial:
                                                    intento 1 → 15 minutos
                                                    intento 2 → 1 hora
                                                    intento 3 → 3 horas
                                                    intento 4 → 8 horas
                                                    intento 5 → 24 horas
                                                    (máximo 3 días según normativa SUNAT)

Cada hora:
    └── Reintenta documentos con error temporal (SUNAT caído, timeout)
            └── Respeta expiración de 3 días
            └── NO reintenta errores permanentes (doc inválido, RUC inactivo)
```

---

### 3.4 ⚠️ Diferencias Dev (Windows) vs Producción (Linux)

| | Windows Dev | VPS Linux Producción |
|---|---|---|
| **Queue driver** | `database` | `redis` |
| **Workers** | `php artisan queue:work` | Horizon via Supervisor |
| **Scheduler** | `php artisan schedule:work` | Cron cada minuto |
| **Horizon UI** | ❌ No disponible | ✅ `tudominio.com/horizon` |
| **Instalar Horizon** | `--ignore-platform-reqs` | Normal |
| **WebSockets** | Variables PowerShell + soketi | PM2 + ecosystem.config.cjs |
| **PHP** | mod_php (XAMPP) | mod_php (Apache2) |

#### Variables Soketi en Windows (PowerShell):

```powershell
$env:SOKETI_DEBUG = "true"
$env:SOKETI_HOST = "127.0.0.1"
$env:SOKETI_PORT = "6001"
$env:SOKETI_DEFAULT_APP_ID = "1"
$env:SOKETI_DEFAULT_APP_KEY = "app-key"
$env:SOKETI_DEFAULT_APP_SECRET = "app-secret"
$env:SOKETI_DEFAULT_APP_ENABLE_CLIENT_MESSAGES = "true"
soketi start
```

---

### 3.5 🏗️ Stack Completo en Producción

| Servicio | Gestor | Usuario | Puerto |
|---|---|---|---|
| **Apache2** | systemd | www-data | 80, 443 |
| **MySQL** | systemd | mysql | 3306 |
| **Redis** | systemd | redis | 6379 |
| **Horizon** (Laravel Queue) | Supervisor | root | - |
| **Soketi** (WebSockets) | PM2 | deploy | 6001 |
| **Scheduler** | Cron | root | - |

```bash
# Verificar estado completo del stack de una sola vez
sudo supervisorctl status && \
su - deploy -c "pm2 list" && \
redis-cli ping && \
sudo systemctl status apache2 --no-pager
```
