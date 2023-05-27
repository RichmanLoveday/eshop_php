const ordersItem = document.querySelector('.orders_items');

const show_detials = function (e) { // console.log(e.target);
    var row = e.target.parentNode;
    if (row.tagName != 'TR') 
        row = row.parentNode;
    
    var orderDetails = row.querySelector('.order_details');

    // get all rows
    var rows = e.currentTarget.querySelectorAll('.order_details');
    console.log(rows);
    for (let index = 0; index < rows.length; index++) {
        if (rows[index] != orderDetails) 
            rows[index].classList.add('hide');
        
    }

    orderDetails ?. classList.toggle('hide');
}
ordersItem.addEventListener('click', show_detials);
