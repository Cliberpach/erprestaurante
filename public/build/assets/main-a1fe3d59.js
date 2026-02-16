let l=null;function i(o){l=o}function u(){return l}window.getCustomerSelect=u;function m(o,a){const c=new TomSelect(`#${a}`,{valueField:"id",options:[o],items:[o.id],labelField:"full_name",searchField:["full_name"],plugins:["clear_button"],placeholder:"Seleccione un cliente",maxOptions:20,create:!1,preload:!1,load:async(e,t)=>{if(e.length<3)return t();try{const r=route("tenant.utils.searchCustomer",{q:e}),n=await fetch(r);if(!n.ok)throw new Error("Error al buscar clientes");const s=(await n.json()).data??[];t(s),s.length===0&&(customerParams.documentSearchCustomer=e,console.log("No se encontró en BD. Guardado:",window.typedCustomer))}catch(r){console.error("Error cargando clientes:",r),t()}},render:{option:(e,t)=>`
                        <div>
                            <strong>${t(e.full_name)}</strong><br>
                            <small>${t(e.email??"")}</small>
                        </div>
                    `,item:(e,t)=>`<div>${t(e.full_name)}</div>`,no_results:function(e,t){return`
                            <div class="no-results">
                                <i class="fas fa-search" style="margin-right:6px; color:#17a2b8;"></i>
                                Sin resultados
                            </div>
                        `}}});i(c)}export{l as c,m as l};
