import{r as l}from"./routes-1d6a2dd6.js";function p(){return window.innerWidth>=992}window.isDesktop=p;const d=window.matchMedia("(min-width: 992px)");let u=d.matches,c=[],r=null,m=null;const n={inputQuantity:document.querySelector("#cantidad"),inputProduct:document.querySelector("#producto"),inputPurchasePrice:document.querySelector("#purchase_price"),inputSalePrice:document.querySelector("#sale_price"),inputQuantity:document.querySelector("#cantidad"),inputStock:document.querySelector("#item_stock"),inputObservation:document.querySelector("#observation_item"),imgQrPayment:document.querySelector("#img-qr-payment"),inputVoucher:document.querySelector("#voucher")};function v(t){u=t}function b(t){c=t}function y(){return c}function i(t){r=t}function D(){return r}function A(t){m=t}function f(){return m}window.getLstDetail=y;window.setDtDetail=i;window.getDtDetail=D;window.setLstDetail=b;window.getCustomerSelect=f;function E(t){t.matches!==u&&(v(t.matches),w(c))}function w(t){const e=document.getElementById("tbl_order_detail"),a=document.getElementById("cards_dishes");!e||!a||(isDesktop()?(e.classList.remove("d-none"),a.classList.add("d-none"),clearTable("tbl_order_detail"),i(destroyDataTable(r)),paintTblDetail(t),i(loadDataTableSimple("tbl_order_detail"))):(e.classList.add("d-none"),a.classList.remove("d-none"),clearTable("tbl_order_detail"),i(destroyDataTable(r)),paintCardsDetail(t)))}function I(){d.addEventListener("change",t=>{E(t)})}async function R({warehouseId:t,productId:e,quantity:a,orderId:o=null}){try{const s=await axios.get(l.validateProductStock({warehouseId:t,productId:e,quantity:a,orderId:o}));return s.data.success?s:(toastr.error(s.data.message,"ERROR EN EL SERVIDOR"),null)}catch(s){return toastr.error(s,"ERROR EN LA PETICIÓN VALIDAR STOCK PRODUCTO"),null}}async function g({programmingId:t,dishId:e,quantity:a,orderId:o=null}){try{const s=await axios.get(l.validateDishStock({programmingId:t,dishId:e,quantity:a,orderId:o}));return s.data.success?s:(toastr.error(s.data.message,"ERROR EN EL SERVIDOR"),null)}catch(s){return toastr.error(s,"ERROR EN LA PETICIÓN VALIDAR STOCK PLATO"),null}}async function O(t){try{const e=await axios.get(l.getBankAccountPayment(t));return e.data.success?(toastr.info(e.data.message,"OPERACIÓN COMPLETADA"),e):(toastr.error(e.data.message,"ERROR EN EL SERVIDOR"),null)}catch(e){return toastr.error(e,"ERROR EN LA PETICIÓN OBTENER CUENTA BANCARIA ACTIVA"),null}}window.validateProductStock=R;window.validateDishStock=g;function h(t){const e=document.getElementById("cards_dishes");e.innerHTML="",t.forEach(a=>{e.innerHTML+=`
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
        `})}function T(t){n.inputProduct.value="",n.inputSalePrice.value="",n.inputQuantity.value="",n.inputStock.value="",n.inputObservation.value="",t.id=null,t.name=null,t.type_name=null,t.quantity=null,t.type_item=null,t.total=null,clearDishSelected(),clearProductSelected()}window.paintCardsDetail=h;export{g as a,I as b,T as c,r as d,n as e,A as f,O as g,b as h,p as i,c as l,h as p,i as s,R as v};
