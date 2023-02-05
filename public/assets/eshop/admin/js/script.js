// const axios = require('axios');
const success_modal = document.querySelector('.success_modal');
const modal_message = document.querySelector('.modal_message');
const overlay = document.querySelector('.overlay');
const btnCloseModal = document.querySelector('.close_modal') || null;
const btnCloseEditModal = document.querySelector('.close_edit_modal') || null;
const errorFiled = document.querySelector('.errorFild');
const tableBody = document.querySelector('.table_body');



const form = document.querySelector('.form');
const rowDelete = document.querySelector('.row_delete');
const rowEdit = document.querySelector('.row_edit');
let handle_result;

// console.log(btnAddCategory);
// Open modal and overlay
const show_modal = function (modal, input = null) {
    console.log('Yesssss');
    console.log(modal)
    console.log(overlay);
    console.log(input);
    modal.classList.toggle('show');
    overlay.classList.toggle('show');

    // focus input
    if (input === null) return;
    input.focus()

    form.addEventListener('onsubmit', function (e) {
        console.log(e);
        e.preventDefault();
    });
};

// Close modal
const close_modal = function (modal, overlay, input = null,) {
    console.log(modal);
    modal.classList.remove('show');
    overlay.classList.remove('show');

    console.log(input);
    if (!input === null) input.value = '';

    // clear error messageh
    errorFiled.classList.remove('error');
    errorFiled.textContent = '';
};


// Send result ajax
const send_data = async function (url, data = {}, handle_result) {
    // console.log(url)
    const form = new FormData();
    form.append('data', data)
    const res = await axios.post(url, data, {
        "Content-Type": "multipart/form-data"
    });

    console.log(res);

    handle_result(res.data);
};


const send_data_files = async function (url, data, handle_result) {
    // console.log(url)
    const res = await axios.post(url, data, {
        "Content-Type": "multipart/form-data"
    });

    console.log(res);

    handle_result(res.data);
};

// Get result ajax
const get_data = async function (url, data = {}) {
    console.log(data);
    const res = await axios.post(url, data, {
        "Content-Type": "multipart/form-data"
    });
    return res.data;
    // handle_result(res.data);
};

const collect_data = function (input, errMsg, data_type, handle_result, e) {
    //console.log('clicked');


    if (data_type === 'add_category') {
        const url = e.target.dataset.url;
        if (input.value.trim() === '' || !isNaN(input.value)) {
            // alert(errMsg);

            // add error field
            errorFiled.classList.toggle('error');
            errorFiled.textContent = errMsg;

            setTimeout(() => {
                errorFiled.classList.remove('error');
                errorFiled.textContent = '';
            }, 2000)

        } else {
            // clear error field
            errorFiled.classList.remove('error');
            errorFiled.textContent = ''

            // Clean data and send to ajax
            const data = input.value.trim();
            console.log(data, url);
            send_data(url, { data: data, data_type: data_type }, handle_result);
        }
    }


    if (data_type === 'add_product') {
        const data = new FormData();
        const url = e.target.dataset.url;
        let productName, productQuantity, productCategory, productPrice, productImage, productImage2, productImage3, productImage4;

        // Loop through array of input and store values in variables
        console.log(input.productImage3);
        console.log(Object.keys(input));
        Object.keys(input).forEach(val => {
            if (val === 'productName') productName = input.productName;
            if (val === 'productQuantity') productQuantity = input.productQuantity;
            if (val === 'productCategory') productCategory = input.productCategory;
            if (val === 'productPrice') productPrice = input.productPrice;
            if (val === 'productImage') productImage = input.productImage;
            if (val === 'productImage2') productImage2 = input.productImage2;
            if (val === 'productImage3') productImage3 = input.productImage3;
            if (val === 'productImage4') productImage4 = input.productImage4;
        });

        console.log(productName, productQuantity, productCategory, productPrice, productImage);
        let err = false;

        // Validate inputs
        if (productName.value.trim() === '' || !isNaN(productName.value)) {
            productName.classList.add('errInput')
            err = true;

        } else {
            productName.classList.remove('errInput');
        }

        if (productQuantity.value.trim() === '') {
            productQuantity.classList.add('errInput')

        } else {
            productQuantity.classList.remove('errInput');
        }

        if (productCategory.value.trim() === '') {
            productCategory.classList.add('errInput')
            err = true;

        } else {
            productCategory.classList.remove('errInput');
        }

        if (productPrice.value.trim() === '') {
            productPrice.classList.add('errInput')
            err = true;

        } else {
            productPrice.classList.remove('errInput');
        }

        if (productImage.files.length === 0) {
            productImage.classList.add('errInput')
            err = true;

        } else {
            productImage.classList.remove('errInput');
        }

        console.log(url, err);
        if (!err) {

            // form data to be sent to post
            data.append('description', productName.value.trim());
            data.append('quantity', productQuantity.value.trim());
            data.append('category', productCategory.value.trim());
            data.append('price', productPrice.value.trim());
            data.append('image', productImage.files[0]);
            data.append('data_type', 'add_product');

            // Check other images
            if (productImage2.files.length > 0) {
                data.append('image2', productImage2.files[0]);
            }

            if (productImage3.files.length > 0) {
                data.append('image3', productImage3.files[0]);
            }

            if (productImage4.files.length > 0) {
                data.append('image4', productImage4.files[0]);
            }

            // Send files throuh ajax
            send_data_files(url, data, handle_result);
        }
    }
};


// Collect edited data
const collect_edit_data = function (url, input, errMsg, handle_result, e) {
    console.log(url);
    console.log(input);
    console.log(errMsg)
    if (input.value.trim() === '' || !isNaN(input.value)) {
        // alert(errMsg);

        // add error field
        errorFiled.classList.toggle('error');
        errorFiled.textContent = errMsg;

        setTimeout(() => {
            errorFiled.classList.remove('error');
            errorFiled.textContent = '';
        }, 2000)

    } else {
        // clear error field
        errorFiled.classList.remove('error');
        errorFiled.textContent = ''

        // Clean data and send to ajax
        const data = input.value.trim();
        const id = input.dataset.rowid;

        send_data(url, { id: id, data: data, data_type: 'edit_category' }, handle_result);
    }
};






