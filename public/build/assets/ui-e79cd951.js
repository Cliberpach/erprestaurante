import{s as l}from"./index-dd82ac63.js";function v(){return window.innerWidth>=992}window.isDesktop=v;const u=window.matchMedia("(min-width: 992px)");let m=u.matches,d=[],r=null,p=null;const i={inputQuantity:document.querySelector("#cantidad"),inputProduct:document.querySelector("#producto"),inputPurchasePrice:document.querySelector("#purchase_price"),inputSalePrice:document.querySelector("#sale_price"),inputQuantity:document.querySelector("#cantidad"),inputStock:document.querySelector("#item_stock"),inputObservation:document.querySelector("#observation_item"),imgQrPayment:document.querySelector("#img-qr-payment"),inputVoucher:document.querySelector("#voucher")};function y(t){m=t}function D(t){d=t}function b(){return d}function o(t){r=t}function f(){return r}function I(t){p=t}function E(){return p}window.getLstDetail=b;window.setDtDetail=o;window.getDtDetail=f;window.setLstDetail=D;window.getCustomerSelect=E;function g(t){t.matches!==m&&(y(t.matches),h(d))}function h(t){const e=document.getElementById("tbl_order_detail"),a=document.getElementById("cards_dishes");!e||!a||(isDesktop()?(e.classList.remove("d-none"),a.classList.add("d-none"),clearTable("tbl_order_detail"),o(destroyDataTable(r)),paintTblDetail(t),o(loadDataTableSimple("tbl_order_detail"))):(e.classList.add("d-none"),a.classList.remove("d-none"),clearTable("tbl_order_detail"),o(destroyDataTable(r)),paintCardsDetail(t)))}function P(){u.addEventListener("change",t=>{g(t)})}const c={validateProductStock:({warehouseId:t,productId:e,quantity:a,orderId:s=null})=>l("tenant.utils.validatedProductStock",{warehouse_id:t,product_id:e,quantity:a,order_id:s}),validateDishStock:({programmingId:t,dishId:e,quantity:a,orderId:s=null})=>l("tenant.utils.validatedDishStock",{programming_id:t,dish_id:e,quantity:a,order_id:s}),getBankAccountPayment:t=>l("tenant.utils.getBackAccountPayment",{payment_method:t})};async function w({warehouseId:t,productId:e,quantity:a,orderId:s=null}){try{const n=await axios.get(c.validateProductStock({warehouseId:t,productId:e,quantity:a,orderId:s}));return n.data.success?n:(toastr.error(n.data.message,"ERROR EN EL SERVIDOR"),null)}catch(n){return toastr.error(n,"ERROR EN LA PETICIÓN VALIDAR STOCK PRODUCTO"),null}}async function S({programmingId:t,dishId:e,quantity:a,orderId:s=null}){try{const n=await axios.get(c.validateDishStock({programmingId:t,dishId:e,quantity:a,orderId:s}));return n.data.success?n:(toastr.error(n.data.message,"ERROR EN EL SERVIDOR"),null)}catch(n){return toastr.error(n,"ERROR EN LA PETICIÓN VALIDAR STOCK PLATO"),null}}async function O(t){try{const e=await axios.get(c.getBankAccountPayment(t));return e.data.success?(toastr.info(e.data.message,"OPERACIÓN COMPLETADA"),e):(toastr.error(e.data.message,"ERROR EN EL SERVIDOR"),null)}catch(e){return toastr.error(e,"ERROR EN LA PETICIÓN OBTENER CUENTA BANCARIA ACTIVA"),null}}window.validateProductStock=w;window.validateDishStock=S;function R(t){const e=document.getElementById("cards_dishes");e.innerHTML="",t.forEach(a=>{e.innerHTML+=`
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
                                data-producto-id="${a.id}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm btnDeleteItem"
                                type="button"
                                data-producto-id="${a.id}">
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
        `})}function T(t){i.inputProduct.value="",i.inputSalePrice.value="",i.inputQuantity.value="",i.inputStock.value="",i.inputObservation.value="",t.id=null,t.name=null,t.type_name=null,t.quantity=null,t.type_item=null,t.total=null,clearDishSelected(),clearProductSelected()}window.paintCardsDetail=R;export{S as a,P as b,T as c,r as d,i as e,I as f,O as g,D as h,v as i,d as l,R as p,o as s,w as v};
