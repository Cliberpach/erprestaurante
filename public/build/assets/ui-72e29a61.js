import{s as o}from"./index-dd82ac63.js";function v(){return window.innerWidth>=992}window.isDesktop=v;const u=window.matchMedia("(min-width: 992px)");let m=u.matches,d=[],l=null;const i={inputQuantity:document.querySelector("#cantidad"),inputProduct:document.querySelector("#producto"),inputPurchasePrice:document.querySelector("#purchase_price"),inputSalePrice:document.querySelector("#sale_price"),inputQuantity:document.querySelector("#cantidad"),inputStock:document.querySelector("#item_stock"),inputObservation:document.querySelector("#observation_item"),imgQrPayment:document.querySelector("#img-qr-payment"),inputVoucher:document.querySelector("#voucher")};function p(t){m=t}function y(t){d=t}function D(){return d}function r(t){l=t}function b(){return l}window.getLstDetail=D;window.setDtDetail=r;window.getDtDetail=b;window.setLstDetail=y;function f(t){t.matches!==m&&(p(t.matches),E(d))}function E(t){const e=document.getElementById("tbl_order_detail"),a=document.getElementById("cards_dishes");!e||!a||(isDesktop()?(e.classList.remove("d-none"),a.classList.add("d-none"),clearTable("tbl_order_detail"),r(destroyDataTable(l)),paintTblDetail(t),r(loadDataTableSimple("tbl_order_detail"))):(e.classList.add("d-none"),a.classList.remove("d-none"),clearTable("tbl_order_detail"),r(destroyDataTable(l)),paintCardsDetail(t)))}function A(){u.addEventListener("change",t=>{f(t)})}const c={validateProductStock:({warehouseId:t,productId:e,quantity:a,orderId:s=null})=>o("tenant.utils.validatedProductStock",{warehouse_id:t,product_id:e,quantity:a,order_id:s}),validateDishStock:({programmingId:t,dishId:e,quantity:a,orderId:s=null})=>o("tenant.utils.validatedDishStock",{programming_id:t,dish_id:e,quantity:a,order_id:s}),getBankAccountPayment:t=>o("tenant.utils.getBackAccountPayment",{payment_method:t}),validationPassword:o("tenant.utils.validationPassword")};async function g({warehouseId:t,productId:e,quantity:a,orderId:s=null}){try{const n=await axios.get(c.validateProductStock({warehouseId:t,productId:e,quantity:a,orderId:s}));return n.data.success?n:(toastr.error(n.data.message,"ERROR EN EL SERVIDOR"),null)}catch(n){return toastr.error(n,"ERROR EN LA PETICIÓN VALIDAR STOCK PRODUCTO"),null}}async function h({programmingId:t,dishId:e,quantity:a,orderId:s=null}){try{const n=await axios.get(c.validateDishStock({programmingId:t,dishId:e,quantity:a,orderId:s}));return n.data.success?n:(toastr.error(n.data.message,"ERROR EN EL SERVIDOR"),null)}catch(n){return toastr.error(n,"ERROR EN LA PETICIÓN VALIDAR STOCK PLATO"),null}}async function S(t){try{const e=await axios.get(c.getBankAccountPayment(t));return e.data.success?(toastr.info(e.data.message,"OPERACIÓN COMPLETADA"),e):(toastr.error(e.data.message,"ERROR EN EL SERVIDOR"),null)}catch(e){return toastr.error(e,"ERROR EN LA PETICIÓN OBTENER CUENTA BANCARIA ACTIVA"),null}}window.validateProductStock=g;window.validateDishStock=h;function w(t){const e=document.getElementById("cards_dishes");e.innerHTML="",t.forEach((a,s)=>{e.innerHTML+=`
            <div class="card mb-2 border-0 shadow-sm" style="height:auto;">
                <div class="card-body py-2">

                    <!-- HEADER -->
                    <div class="d-flex justify-content-between align-items-start mb-2">

                        <!-- NAME + OBSERVATION -->
                        <div class="me-2">
                            <h6 class="fw-bold mb-0">${a.name}</h6>

                            ${a.observation?`<small class="text-muted fst-italic">${a.observation}</small>`:""}
                        </div>

                        <!-- ACTIONS -->
                        <div class="d-flex gap-1">
                            <button class="btn btn-info btn-sm btnEditItem"
                                type="button"
                                data-producto-id="${a.id}"
                                data-index="${s}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm btnDeleteItem"
                                type="button"
                                data-producto-id="${a.id}"
                                data-index="${s}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- LINEA UNICA: CANTIDAD - PRECIO - TOTAL -->
                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">Cant.</small>
                            <div class="fw-semibold">${a.quantity}</div>
                        </div>

                        <div>
                            <small class="text-muted">Precio</small>
                            <div>S/. ${parseFloat(a.sale_price).toFixed(2)}</div>
                        </div>

                        <div class="text-end bg-light rounded px-2 py-1">
                            <small class="text-muted">Total</small>
                            <div class="fw-bold text-primary">
                                S/. ${parseFloat(a.total).toFixed(2)}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        `})}function I(t){i.inputProduct.value="",i.inputSalePrice.value="",i.inputQuantity.value="",i.inputStock.value="",i.inputObservation.value="",t.id=null,t.name=null,t.type_name=null,t.quantity=null,t.type_item=null,t.total=null,clearDishSelected(),clearProductSelected()}window.paintCardsDetail=w;export{h as a,A as b,I as c,l as d,i as e,y as f,S as g,v as i,d as l,w as p,r as s,g as v};
