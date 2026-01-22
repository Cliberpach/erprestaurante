
@vite(['resources/css/loader/loader1.css'])

<div class="loader-overlay active">
     <div class="loader-app">
         <!-- Campanilla con ondas de sonido -->
         <div class="bell-container">
             <!-- Ondas de sonido -->
             <div class="sound-wave"></div>
             <div class="sound-wave"></div>
             <div class="sound-wave"></div>
             <div class="sound-wave"></div>

             <!-- Partículas -->
             <div class="particles">
                 <div class="particle"></div>
                 <div class="particle"></div>
                 <div class="particle"></div>
                 <div class="particle"></div>
                 <div class="particle"></div>
                 <div class="particle"></div>
             </div>

             <!-- Campanilla de servicio realista -->
             <div class="service-bell">
                 <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                     <defs>
                         <!-- Gradiente cromado para la campana -->
                         <linearGradient id="chromeGradient" x1="0%" y1="0%" x2="100%"
                             y2="100%">
                             <stop offset="0%" style="stop-color:#E8E8E8;stop-opacity:1" />
                             <stop offset="30%" style="stop-color:#FFFFFF;stop-opacity:1" />
                             <stop offset="60%" style="stop-color:#D0D0D0;stop-opacity:1" />
                             <stop offset="100%" style="stop-color:#B0B0B0;stop-opacity:1" />
                         </linearGradient>

                         <!-- Gradiente para brillo -->
                         <radialGradient id="shineGradient">
                             <stop offset="0%" style="stop-color:#FFFFFF;stop-opacity:0.9" />
                             <stop offset="100%" style="stop-color:#FFFFFF;stop-opacity:0" />
                         </radialGradient>

                         <!-- Sombra para profundidad -->
                         <filter id="bellShadow">
                             <feGaussianBlur in="SourceAlpha" stdDeviation="3" />
                             <feOffset dx="0" dy="4" result="offsetblur" />
                             <feComponentTransfer>
                                 <feFuncA type="linear" slope="0.3" />
                             </feComponentTransfer>
                             <feMerge>
                                 <feMergeNode />
                                 <feMergeNode in="SourceGraphic" />
                             </feMerge>
                         </filter>
                     </defs>

                     <!-- Base negra de la campanilla -->
                     <ellipse cx="60" cy="100" rx="38" ry="5" fill="#1a1a1a"
                         opacity="0.4" />
                     <path
                         d="M 25 95 Q 25 92 28 92 L 92 92 Q 95 92 95 95 L 95 100 Q 95 103 92 103 L 28 103 Q 25 103 25 100 Z"
                         fill="#1a1a1a" />

                     <!-- Anillo decorativo de la base -->
                     <ellipse cx="60" cy="95" rx="32" ry="3" fill="#2a2a2a" />

                     <!-- Cuerpo principal de la campana (cromado) -->
                     <path d="M 60 30 Q 35 35 30 70 L 30 85 Q 30 90 35 90 L 85 90 Q 90 90 90 85 L 90 70 Q 85 35 60 30 Z"
                         fill="url(#chromeGradient)" filter="url(#bellShadow)" stroke="#A0A0A0" stroke-width="0.5" />

                     <!-- Detalles de profundidad internos -->
                     <path d="M 60 35 Q 40 38 37 68 L 37 82 Q 37 85 40 85 L 80 85 Q 83 85 83 82 L 83 68 Q 80 38 60 35 Z"
                         fill="url(#chromeGradient)" opacity="0.6" />

                     <!-- Reflejo principal (brillo realista) -->
                     <ellipse cx="48" cy="50" rx="15" ry="25" fill="url(#shineGradient)"
                         opacity="0.7" transform="rotate(-25 48 50)" />

                     <!-- Reflejo secundario -->
                     <ellipse cx="72" cy="55" rx="8" ry="15" fill="white" opacity="0.3"
                         transform="rotate(15 72 55)" />

                     <!-- Líneas de detalle cromado -->
                     <path d="M 35 60 Q 33 65 32 75" stroke="white" stroke-width="1" opacity="0.4" fill="none" />
                     <path d="M 85 60 Q 87 65 88 75" stroke="#999" stroke-width="1" opacity="0.4" fill="none" />

                     <!-- Botón superior (cromado) -->
                     <circle cx="60" cy="27" r="10" fill="url(#chromeGradient)" stroke="#A0A0A0"
                         stroke-width="0.5" />
                     <circle cx="60" cy="27" r="7" fill="#D8D8D8" />

                     <!-- Brillo del botón -->
                     <circle cx="57" cy="24" r="3" fill="white" opacity="0.8" />

                     <!-- Borde superior del botón -->
                     <circle cx="60" cy="25" r="9" fill="none" stroke="white" stroke-width="1"
                         opacity="0.3" />

                     <!-- Sombra interna del borde inferior -->
                     <ellipse cx="60" cy="88" rx="28" ry="3" fill="#888888"
                         opacity="0.4" />

                     <!-- Reflejo en el borde inferior -->
                     <path d="M 35 87 Q 60 86 85 87" stroke="white" stroke-width="1.5" opacity="0.5"
                         fill="none" />
                 </svg>
             </div>
         </div>

         <!-- Marca -->
         <div class="brand">
             <div class="brand-name">
                 Comanda<span class="highlight">Pro</span>
             </div>
             <div class="brand-tagline">Sistema de Restaurante</div>
         </div>

         <!-- Mensaje de carga rotativo -->
         <div class="loading-message" id="loadingMessage">Preparando mesas...</div>

         <!-- Puntos de carga flotantes -->
         <div class="loading-dots">
             <div class="dot"></div>
             <div class="dot"></div>
             <div class="dot"></div>
         </div>
     </div>
 </div>

