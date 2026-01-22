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

    /*if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        //document.getElementById('theme-text').textContent = 'Modo Claro';
    }*/

    // Iniciar rotación de mensajes si el loader está activo
    if (document.querySelector('.loader-overlay').classList.contains('active')) {
        startMessageRotation();
    }

    const btnToggleTheme = document.getElementById('ld-theme');
    btnToggleTheme.addEventListener('click', toggleTheme);
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

// Función para cambiar el tema
function toggleTheme() {
    const html = document.documentElement;
    const themeText = document.getElementById('theme-text');
    const currentTheme = html.getAttribute('data-theme');

    if (currentTheme === 'dark') {
        html.removeAttribute('data-theme');
        themeText.textContent = 'Modo Oscuro';
        localStorage.setItem('theme', 'light');
    } else {
        html.setAttribute('data-theme', 'dark');
        themeText.textContent = 'Modo Claro';
        localStorage.setItem('theme', 'dark');
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

