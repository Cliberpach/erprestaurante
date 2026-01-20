import{s as b,l as r,e as d,c as D,i as c,a as i,d as A,p as S,v as _,b as T,f as g}from"./ui-8f47bf0c.js";const y={index:b("tenant.mostrador_mesero.mostrador.index"),store:b("tenant.mostrador_mesero.mostrador.store")};let o={id:null,warehouse_id:null,programming_id:null,name:null,type_name:null,purchase_price:null,sale_price:null,type_item:null,quantity:null,total:null};const l={subTotal:0,tax:0,totalPay:0};function I(t){o=t}function w(){return l}window.setItemSelected=I;window.getAmounts=w;function u(t){let n="";t.forEach(a=>{n+=`<tr>
                            <th>
                                <div class="d-flex justify-content-center gap-1">

                                    <button class="btn btn-info btn-sm btnEditItem" type="button"
                                    data-producto-id="${a.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button class="btn btn-danger btn-sm btnDeleteItem" type="button"
                                    data-producto-id="${a.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>
                            </th>
                            <td>${a.name}</td>
                            <td>${a.type_item}</td>
                            <td>${a.type_name}</td>
                            <td>${formatSoles(a.sale_price)}</td>
                            <td>${a.quantity}</td>
                            <td>${formatSoles(a.total)}</td>
                            <td>${formatSoles(a.purchase_price)}</td>
                        </tr>`});const e=document.querySelector("#tbl_order_detail tbody");e.innerHTML=n}function m(t){document.querySelector("#subtotal_amount").innerText=formatSoles(t.subTotal),document.querySelector("#igv_amount").innerText=formatSoles(t.tax),document.querySelector("#total_amount").innerText=formatSoles(t.totalPay)}window.paintTblDetail=u;async function v(t){if(toastr.clear(),r.length===0){toastr.error("DEBE AGREGAR AL MENOS UN PRODUCTO EN EL DETALLE!!");return}if((await Swal.fire({title:"¿Desea registrar el pedido?",text:"Confirme para continuar",icon:"question",showCancelButton:!0,confirmButtonText:"SI",cancelButtonText:"NO",reverseButtons:!0,customClass:{confirmButton:"btn btn-primary",cancelButton:"btn btn-secondary"},buttonsStyling:!1})).isConfirmed)try{clearValidationErrors("msgError"),Swal.fire({title:"Registrando pedido...",text:"Por favor espere",allowOutsideClick:!1,allowEscapeKey:!1,didOpen:()=>{Swal.showLoading()}});const e=new FormData(t);e.append("lst_detail",JSON.stringify(r)),e.append("table_id",app.tableId);const a=await axios.post(y.store,e);a.data.success?(toastr.success(a.data.message,"OPERACIÓN COMPLETADA"),window.location.href=y.index):(toastr.error(a.data.message,"ERROR EN EL SERVIDOR"),Swal.close())}catch(e){if(Swal.close(),e.response&&e.response.status===422){const a=e.response.data.errors;paintValidationErrors(a,"error");return}}else Swal.fire({icon:"info",title:"Operación cancelada",text:"No se realizaron acciones.",confirmButtonText:"OK",customClass:{confirmButton:"btn btn-secondary"},buttonsStyling:!1})}async function O(){toastr.clear(),mostrarAnimacion1();const t=d.inputQuantity;o.quantity=t.value;const n=await R();o.total=o.sale_price*parseFloat(t.value),o.observation=d.inputObservation.value.trim(),n&&(C({...o},t.value),f(l),m(l),D(o)),ocultarAnimacion1()}function C(t,n){if(t.quantity=n,r.findIndex(a=>a.id==t.id&&a.type_item===a.type_item)!==-1){toastr.error(`EL ${t.type_name} YA EXISTE EN EL DETALLE`);return}r.push(t),c()?(clearTable("tbl_order_detail"),i(destroyDataTable(A)),u(r),i(loadDataTableSimple("tbl_order_detail"))):S(r),toastr.info(`${t.type_name} AGREGADO AL DETALLE`)}function f(t){let n=app.companyIgv,e=0,a=0,s=0;console.log(r),r.forEach(p=>{e+=parseFloat(p.total)}),s=e/((100+n)/100),a=e-s,t.subTotal=s,t.tax=a,t.totalPay=e}function h(t){toastr.clear();const n=t.getAttribute("data-producto-id");L(n)&&(c()?(clearTable("tbl_order_detail"),i(destroyDataTable(A)),u(r),i(loadDataTableSimple("tbl_order_detail"))):S(r),f(l),m(l),toastr.success("ITEM ELIMINADO!!"))}function L(t){const n=r.findIndex(e=>e.id==t);return n===-1?(toastr.error("NO SE ENCONTRÓ EL ITEM EN EL DETALLE!!!"),!1):(r.splice(n,1),!0)}async function R(){if(!o.id)return toastr.error("DEBE SELECCIONAR UN PLATO O PRODUCTO PREVIAMENTE"),!1;const t=d.inputQuantity;if(!t.value)return toastr.error("DEBE INGRESAR UNA CANTIDAD!!"),!1;if(t.value==0)return toastr.error("LA CANTIDAD DEBE SER MAYOR A 0!!"),!1;if(d.inputObservation.value.trim().length>20)return toastr.error("OBSERVACIÓN MÁX PERMITIDA 20 CARACTERES"),!1;if(o.type_item==="PRODUCTO"){const e={warehouseId:o.warehouse_id,productId:o.id,quantity:o.quantity},a=await _(e);if(!a||!a.data.success)return}if(o.type_item==="PLATO"){const e={programmingId:o.programming_id,dishId:o.id,quantity:o.quantity},a=await T(e);if(!a||!a.data.success)return}return!0}window.calculateAmounts=f;window.paintAmounts=m;function N(){app.init(),g(),x(),P()}function x(){document.querySelector("#form_create").addEventListener("submit",t=>{t.preventDefault(),v(t.target)})}function P(){document.addEventListener("click",t=>{t.target.closest(".btnAgregarProducto")&&O();const n=t.target.closest(".btnDeleteItem");n&&h(n)})}function $(){const t=app.customerFormatted;window.clientSelect=new TomSelect("#client_id",{valueField:"id",options:[t],items:[t.id],labelField:"full_name",searchField:["full_name"],plugins:["clear_button"],placeholder:"Seleccione un cliente",maxOptions:20,create:!1,preload:!1,onType:n=>{lastCustomerQuery=n},load:async(n,e)=>{if(n.length<3)return e();try{const a=`{{ route('tenant.utils.searchCustomer') }}?q=${encodeURIComponent(n)}`,s=await fetch(a);if(!s.ok)throw new Error("Error al buscar clientes");const E=(await s.json()).data??[];e(E),E.length===0&&(customerParams.documentSearchCustomer=lastCustomerQuery,console.log("No se encontró en BD. Guardado:",window.typedCustomer))}catch(a){console.error("Error cargando clientes:",a),e()}},render:{option:(n,e)=>`
                        <div>
                            <strong>${e(n.full_name)}</strong><br>
                            <small>${e(n.email??"")}</small>
                        </div>
                    `,item:(n,e)=>`<div>${e(n.full_name)}</div>`,no_results:function(n,e){return`
                            <div class="no-results">
                                <i class="fas fa-search" style="margin-right:6px; color:#17a2b8;"></i>
                                Sin resultados
                            </div>
                        `}}})}document.addEventListener("DOMContentLoaded",()=>{mostrarAnimacion1(),$(),c()&&i(loadDataTableSimple("tbl_order_detail")),N(),ocultarAnimacion1()});
