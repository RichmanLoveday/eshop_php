const country = document.querySelector('.countryList');
const state = document.querySelectorAll('.stateList');

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

        if (obj.data_type === 'increase_quantity' || obj.data_type === 'decrease_quantity' || obj.data_type === 'remove_cart' || obj.data_type === 'edit_quantity') {
            if (typeof obj.message_type !== 'undefined') {
                console.log(obj);
                // update cart datas
                cartDatas.innerHTML = obj.products_details.products;
                subTotal.forEach((ele, index) => {
                    ele.textContent = (index == 0) ? `Sub Total: $${obj.products_details.sub_total}` : `$${obj.products_details.sub_total}`;

                })
                console.log(total);
                total.textContent = `$${obj.products_details.sub_total}`;
            }
        }
    }
}

const select_country = function (e) {
    //e.preventDefault();

    // check add_class_cart
    if (!e.target.classList.contains('add-to-cart')) return;

    //get id, url
    console.log(e.target);
    const id = e.target.dataset.id;
    const url = e.target.dataset.url;
    // console.log(id);
    // console.log(url);

    // ajax data to php
    send_data(url, { id: id, data_type: 'add_to_cart' }, handle_result)

    // handle the result coming back
}

country?.addEventListener('click', add_to_cart);


const select_state = function (e) {
    //e.preventDefault();

    // check add_class_cart
    if (!e.target.classList.contains('add-to-cart')) return;

    //get id, url
    console.log(e.target);
    const id = e.target.dataset.id;
    const url = e.target.dataset.url;
    // console.log(id);
    // console.log(url);

    // ajax data to php
    send_data(url, { id: id, data_type: 'add_to_cart' }, handle_result)

    // handle the result coming back
}

state?.addEventListener('click', add_to_cart);


