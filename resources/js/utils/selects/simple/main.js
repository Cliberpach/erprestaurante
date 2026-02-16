export function loadSimpleSelect(id, icon) {
    const simpleSelect = document.getElementById(id);
    if (simpleSelect && !simpleSelect.tomselect) {
        const instance = new TomSelect(simpleSelect, {
            valueField: 'id',
            labelField: 'text',
            searchField: ['text', 'id'],
            create: false,
            sortField: {
                field: 'id',
                direction: 'desc'
            },
            plugins: ['clear_button'],
            render: {
                option: (item, escape) =>
                    `
                            <div>
                                ${icon ? icon : ''}
                                ${escape(item.text)}
                            </div>
                        `
                ,
                item: (item, escape) => `
                            <div>
                                ${icon ? icon : ''}
                                ${escape(item.text)}
                            </div>
                        `
            }
        });
        return instance;
    }
    return null;
}
