import{s as u}from"./index-dd82ac63.js";const f={count:"notifications.count",getAll:"notifications.getAll",notificationIndex:"tenant.consultas.notificaciones.index",notified:"notifications.notified"};async function m(t){try{const{data:e}=await axios.get(u(f.getAll),{params:{page:t}});return e}catch(e){return toastr.error(e,"ERROR EN LA PETICIÓN OBTENER CANTIDAD DE NOTIFICACIONES"),null}}const l=appMain.tenantId;let b=0;const g=new Audio("/assets/sounds/bell-notification-337658.mp3"),i={button:document.getElementById("notificationButton"),count:document.getElementById("notificationCount"),countHeader:document.getElementById("notificationCountHeader"),list:document.getElementById("notificationList"),markAllBtn:document.getElementById("markAllAsRead"),scrollContainer:null};let a={currentPage:1,isLoading:!1,hasMore:!0,totalCount:0};function p(t){Swal.mixin({toast:!0,position:"bottom-end",showConfirmButton:!1,showCloseButton:!0,timer:8e3,timerProgressBar:!0,customClass:{popup:"facebook-toast",title:"facebook-toast-title",htmlContainer:"facebook-toast-content",closeButton:"facebook-toast-close"}}).fire({icon:"info",title:"Nueva notificación",html:`
            <div class="toast-alert-body">
                <p class="mb-0 text-muted">${t.content}</p>
            </div>
        `})}function h(t){const e=document.querySelector(".list-group-hover");if(!e)return;const o=document.createElement("li");o.className="list-group-item d-flex justify-content-between align-items-center notification-new",o.innerHTML=`
        <div class="avatar avatar-xs bg-primary rounded-circle text-white">
            <i class="fi fi-rr-bell"></i>
        </div>
        <div class="me-auto ms-2">
            <h6 class="mb-0">Nueva notificación</h6>
            <small class="text-body d-block">${t.content}</small>
            <small class="text-muted position-absolute end-0 top-0 me-3 mt-2">Ahora</small>
        </div>
    `,e.insertBefore(o,e.firstChild),setTimeout(()=>{o.classList.add("notification-show")},10),setTimeout(()=>{o.classList.remove("notification-new")},5e3)}function C(t){t++;const e=document.querySelector(".dropdown-menu h6 .badge");e&&(e.textContent=t,e.classList.add("badge-pulse"),setTimeout(()=>{e.classList.remove("badge-pulse")},1e3))}function E(){const t=document.querySelector(".fi-rr-bell");t&&(t.classList.add("bell-ring"),setTimeout(()=>{t.classList.remove("bell-ring")},1e3))}function v(){i.list.innerHTML=`
        <li class="list-group-item text-center text-muted">
            <div class="spinner-border spinner-border-sm mb-2" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <div>Cargando notificaciones...</div>
        </li>
    `}function y(t,e=!1){const{count:o,notifications:c}=t;if(i.count.textContent=o,i.countHeader.textContent=o,i.count.style.display=o>0?"block":"none",e||(i.list.innerHTML=""),c.length===0&&!e){i.list.innerHTML=`
            <li class="list-group-item text-center text-muted">
                <i class="fi fi-rr-bell fs-3 d-block mb-2"></i>
                No hay notificaciones
            </li>
        `;return}if(c.forEach(n=>{const d=N(n.type_object),s=document.createElement("li");s.className="list-group-item d-flex justify-content-between align-items-start position-relative notification-item",s.dataset.id=n.id,s.dataset.type=n.type_object,s.dataset.objectId=n.object_id,s.innerHTML=`
            <div class="avatar avatar-xs ${d.bgClass} rounded-circle text-white">
                <i class="${d.icon}"></i>
            </div>
            <div class="flex-grow-1 ms-2">
                <h6 class="mb-1">${r(n.type)}</h6>
                <small class="text-body d-block">${r(n.content||"")}</small>
                <small class="text-muted">${r(n.time_ago)}</small>
            </div>
            <button class="btn btn-sm btn-link text-muted p-0 mark-as-read"
                    data-id="${n.id}"
                    title="Marcar como leída">
                <i class="bi bi-x-lg"></i>
            </button>
        `,i.list.appendChild(s)}),!a.hasMore&&e&&c.length>0){const n=document.createElement("li");n.className="list-group-item text-center text-muted small",n.innerHTML=`
            <i class="fi fi-rr-check-circle"></i> No hay más notificaciones
        `,i.list.appendChild(n)}}function N(t){return{ORDEN_TRABAJO:{icon:"fi fi-rr-tool-box",bgClass:"bg-primary"},COTIZACION:{icon:"fi fi-rr-calculator",bgClass:"bg-info"},VENTA:{icon:"fi fi-rr-shopping-cart",bgClass:"bg-success"},PRODUCCION:{icon:"fi fi-rr-settings",bgClass:"bg-warning"},COMPRA:{icon:"fi fi-rr-shopping-bag",bgClass:"bg-secondary"}}[t]||{icon:"fi fi-rr-bell",bgClass:"bg-dark"}}function w(){i.list.innerHTML=`
        <li class="list-group-item text-center text-danger">
            <i class="fi fi-rr-cross-circle fs-3 d-block mb-2"></i>
            Error al cargar notificaciones
        </li>
    `}function r(t){const e=document.createElement("div");return e.textContent=t,e.innerHTML}function A(){}function x(t){I(),p(t),h(t),C(b),E()}function I(){new Audio("/assets/sounds/bell-notification-337658.mp3").play().catch(e=>console.log("No se pudo reproducir el sonido"))}async function L(){a.currentPage=1,a.hasMore=!0,v();try{console.log("🔔 Cargando notificaciones del servidor...");const t=await m(1);console.log("✅ Notificaciones recibidas:",t),a.hasMore=t.has_more,a.totalCount=t.count,y(t,!1)}catch(t){console.error("❌ Error al cargar notificaciones:",t),w()}}function T(){var e;if(document.body.addEventListener("click",A,{once:!0}),window.Echo.connector.pusher.connection.bind("connected",()=>{console.log("✅ CONECTADO")}),window.Echo.connector.pusher.connection.bind("disconnected",()=>{console.log("❌ DESCONECTADO")}),window.Echo.connector.pusher.connection.bind("error",o=>{console.error("❌ ERROR:",o)}),!l){console.warn("⚠️ Tenant no definido");return}const t=window.Echo.private(`alerts.${l}`);t.subscribed(()=>{console.log("✅ SUSCRITO AL CANAL PRIVADO tenant."+l)}),t.error(o=>{console.error("❌ ERROR EN CANAL PRIVADO:",o)}),t.listen(".alert.created",o=>{console.log("🔥 ALERTA RECIBIDA:",o),x(o)}),(e=i.button)==null||e.addEventListener("click",()=>{L()})}document.addEventListener("DOMContentLoaded",()=>{g.volume=.5,"Notification"in window&&Notification.permission==="default"&&Notification.requestPermission(),T()});
