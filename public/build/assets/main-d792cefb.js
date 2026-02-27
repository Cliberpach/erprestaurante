function c(d,e){const t=document.getElementById(d);return t&&!t.tomselect?new TomSelect(t,{valueField:"id",labelField:"text",searchField:["text","id"],create:!1,sortField:{field:"id",direction:"desc"},plugins:["clear_button"],render:{option:(l,i)=>`
                            <div>
                                ${e||""}
                                ${i(l.text)}
                            </div>
                        `,item:(l,i)=>`
                            <div>
                                ${e||""}
                                ${i(l.text)}
                            </div>
                        `}}):null}export{c as l};
