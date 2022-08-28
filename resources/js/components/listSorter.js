$('.listSorter').each((index,el) => {
    const model = $(el).data('model');
    $(el).sortable({
        stop: () => {
            let updatedOrder = {};
            updatedOrder.model = model;
            updatedOrder.order = [];
            $(el).children().each((index,el) => {
                let itemOrder = {};
                itemOrder.elementId = $(el).data('listSorterId');
                itemOrder.elementOrder = index;
                updatedOrder.order.push(itemOrder);
            });
            saveNewOrder(updatedOrder)
        },
        handle: '.listSorterHandle'
    });
});

/**
 * Pass an object here containing two properties: model and order.
 * The model property is the name of the model involved (used in the AJAX route, defined in routes/wep.php).
 * The order property is whatever data you want to pass to the "reorder" function in that model's controller.
 * @param orderObject
 */
const saveNewOrder = orderObject => {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const url = `/${orderObject.model}s/reorder`;
    $.ajax({
        url: url,
        headers: {
            'X-CSRF-Token': csrfToken,
        },
        method: 'POST',
        data: {order:orderObject.order},
        error: response => {
            console.log('Reordering error: ',response);
        }
    });
};
