const loadingMessages = [
    "Preparando mesas...",
    "Calentando las parrillas...",
    "Afilando los cuchillos...",
    "Organizando comandas...",
    "Limpiando la cocina...",
    "Preparando ingredientes frescos...",
    "Verificando el inventario...",
    "Configurando el menú del día...",
    "Sincronizando pedidos...",
    "Preparando el área de bar...",
    "Organizando las reservas...",
    "Activando el sistema de cocina...",
    "Cargando recetas especiales...",
    "Preparando el servicio...",
    "Verificando la carta de vinos...",
    "Configurando las estaciones...",
    "Inicializando el punto de venta...",
    "Preparando el área de postres...",
    "Organizando los turnos...",
    "Verificando temperaturas...",
    "Activando notificaciones...",
    "Configurando las mesas...",
    "Preparando el servicio express...",
    "Cargando el sistema de propinas...",
    "Verificando productos del día...",
    "Sincronizando con cocina...",
    "Preparando reportes...",
    "Activando modo restaurante...",
    "Configurando preferencias...",
    "¡Casi listo para servir!"
];

let messageInterval;
let currentMessageIndex = 0;
let savedTheme = localStorage.getItem('theme');

// Cargar tema guardado
document.addEventListener('DOMContentLoaded', function () {

    showSessionMessages();
    eventsMenu();

    // Restaurar tema guardado sincronizando ambos atributos
    if (savedTheme === 'dark') {
        applyTheme(true);
    }

    // Iniciar rotación de mensajes si el loader está activo
    if (document.querySelector('.loader-overlay').classList.contains('active')) {
        startMessageRotation();
    }

    // Sincronizar data-theme cuando Bootstrap cambia data-bs-theme via [data-bs-theme-value]
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-bs-theme-value]');
        if (!btn) return;

        const value = btn.getAttribute('data-bs-theme-value');
        if (value === 'dark') {
            applyTheme(true);
        } else if (value === 'light') {
            applyTheme(false);
        } else {
            // auto: seguir preferencia del sistema
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(prefersDark);
        }
    });
});

// Función para cambiar mensaje aleatorio
function changeLoadingMessage() {
    const messageElement = document.getElementById('loadingMessage');

    // Seleccionar mensaje aleatorio diferente al actual
    let newIndex;
    do {
        newIndex = Math.floor(Math.random() * loadingMessages.length);
    } while (newIndex === currentMessageIndex && loadingMessages.length > 1);

    currentMessageIndex = newIndex;

    // Aplicar animación de fade
    messageElement.style.animation = 'none';
    setTimeout(() => {
        messageElement.textContent = loadingMessages[currentMessageIndex];
        messageElement.style.animation = 'messageFade 0.5s ease-in-out';
    }, 50);
}

// Iniciar rotación de mensajes
function startMessageRotation() {
    // Cambiar mensaje cada 2 segundos
    messageInterval = setInterval(changeLoadingMessage, 2000);
}

// Detener rotación de mensajes
function stopMessageRotation() {
    if (messageInterval) {
        clearInterval(messageInterval);
    }
}

// Aplica ambos atributos de tema de forma sincronizada
function applyTheme(isDark) {
    const html = document.documentElement;
    if (isDark) {
        html.setAttribute('data-theme', 'dark');
        html.setAttribute('data-bs-theme', 'dark');
        localStorage.setItem('theme', 'dark');
    } else {
        html.removeAttribute('data-theme');
        html.removeAttribute('data-bs-theme');
        localStorage.setItem('theme', 'light');
    }
}

// Función para cambiar el tema (botón toggle simple)
function toggleTheme() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    applyTheme(!isDark);
    const themeText = document.getElementById('theme-text');
    if (themeText) {
        themeText.textContent = !isDark ? 'Modo Claro' : 'Modo Oscuro';
    }
}


// Toggle loader
function toggleLoader() {
    const loader = document.querySelector('.loader-overlay');
    const isActive = loader.classList.toggle('active');

    if (isActive) {
        startMessageRotation();
    } else {
        stopMessageRotation();
    }
}

// Simular carga automática
window.addEventListener('load', function () {
    document.querySelector('.loader-overlay').classList.remove('active');
    stopMessageRotation();
});

function eventsMenu() {
    document.addEventListener('click', function (e) {
        const menuLink = e.target.closest('.menu-click');
        if (!menuLink) return;
        document.querySelector('.loader-overlay').classList.add('active');
        startMessageRotation();
    });
}

function showSessionMessages() {
    if (!window.sessionMessages) return;

    const { success, error } = window.sessionMessages;

    if (success) {
        Swal.fire({
            icon: 'success',
            title: 'OPERACIÓN COMPLETADA',
            text: success,
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    }

    if (error) {
        Swal.fire({
            icon: 'error',
            title: 'ERROR EN LA OPERACIÓN',
            text: error,
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    }
}

