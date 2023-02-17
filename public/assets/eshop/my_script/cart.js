const featuresItem = document.querySelector('.features_items');
const cartQuantityUp = document.querySelector('.cart_quantity_up');
const cartQuantityDown = document.querySelector('.cart_quantity_down');
const cartDatas = document.querySelector('.cart_datas');
const cartQuantityInput = document.querySelector('.cart_quantity_input');

handle_result = function (result) {
    console.log(typeof result)
    const obj = result;
    if (obj !== '') {
        if (obj.data_type === 'add_to_cart') {
            if (typeof obj.message_type !== 'undefined') {
                let timerInterval
                Swal.fire({
                    position: 'top-end',
                    html: `<div style="font-size: 15px; padding: 10px; color: #FE980F;">${obj.message}</div>`,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,

                    willClose: () => {
                        clearInterval(timerInterval)
                    }
                },)
            }
        }

        if (obj.data_type === 'increase_quantity') {
            if (typeof obj.message_type !== 'undefined') {
                console.log(obj);
                // update cart datas
                cartDatas.innerHTML = obj.products;
                console.log(cartDatas);
            }
        }

        if (obj.data_type === 'decrease_quantity') {
            if (typeof obj.message_type !== 'undefined') {
                console.log(obj);
                // update cart datas
                cartDatas.innerHTML = obj.products;
                console.log(cartDatas);
            }
        }
    }
}

const add_to_cart = function (e) {
    //e.preventDefault();

    // check add_class_cart
    if (!e.target.classList.contains('add-to-cart')) return;

    //get id, url
    console.log(e.target);
    const id = e.target.dataset.id;
    const url = e.target.dataset.url;
    console.log(id);
    console.log(url);

    // ajax data to php
    send_data(url, { id: id, data_type: 'add_to_cart' }, handle_result)

    // handle the result coming back
}

featuresItem?.addEventListener('click', add_to_cart);


const increase_quantity = function (e) {
    e.preventDefault();
    console.log(e);
    let url, id, data_type;
    // check for class content
    if (e.target.classList.contains('cart_quantity_up')) {
        console.log(e.target);
        // get url, id, input value
        id = e.target.dataset.id;
        url = e.target.dataset.url;
        data_type = 'increase_quantity';
        console.log(url);

    }

    if (e.target.classList.contains('cart_quantity_down')) {
        console.log(e.target);
        // get url, id, input value
        id = e.target.dataset.id;
        url = e.target.dataset.url;
        data_type = 'decrease_quantity';
        console.log(url);
    }

    if (e.target.classList.contains('cart_quantity_delete')) {
        console.log(e.target);
        // get url, id, input value
        id = e.target.dataset.id;
        url = e.target.dataset.url;
        data_type = 'decrease_quantity';
        console.log(url);
    }

    // send to ajax
    send_data(url, { id: id, data_type: data_type }, handle_result);

}
cartDatas?.addEventListener('click', increase_quantity);