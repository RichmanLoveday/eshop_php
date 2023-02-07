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
    //console.log(input);
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


    if (modal.classList.contains('myModal') || modal.classList.contains('edit_category')) {

        modal.classList.remove('show');
        overlay.classList.remove('show');
        input.value = ''

        return;
    }

    // Check form for file input modals
    if (modal.classList.contains('edit_product_modal') || modal.classList.contains('product_modal')) {
        modal.classList.remove('show');
        overlay.classList.remove('show');
        console.log(input)
    }

    // for sucess modal
    modal.classList.remove('show');
    overlay.classList.remove('show');


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
    console.log(url);
    console.log(data);
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
        const url = e.target.dataset.url;
        const form = new FormData();        // Form data
        console.log(url);
        console.log(input)
        let err = false;

        // Validate inputs
        if (input.productName.value.trim() === '' || !isNaN(input.productName.value)) {
            input.productName.classList.add('errInput')
            err = true;

        } else {
            input.productName.classList.remove('errInput');
        }

        if (input.productQuantity.value.trim() === '') {
            input.productQuantity.classList.add('errInput')

        } else {
            input.productQuantity.classList.remove('errInput');
        }

        if (input.productCategory.value.trim() === '') {
            input.productCategory.classList.add('errInput')
            err = true;

        } else {
            input.productCategory.classList.remove('errInput');
        }

        if (input.productPrice.value.trim() === '') {
            input.productPrice.classList.add('errInput')
            err = true;

        } else {
            input.productPrice.classList.remove('errInput');
        }

        console.log(input.productImage.src, input.productImage.files);
        if (input.productImage.files.length === 0) {
            input.productImage.classList.add('errInput')
            err = true;

        } else {
            input.productImage.classList.remove('errInput');
        }

        console.log(url, err);
        if (!err) {

            // form data to be sent to post
            console.log(form);
            form.append('description', input.productName.value.trim());
            form.append('quantity', input.productQuantity.value.trim());
            form.append('category', input.productCategory.value.trim());
            form.append('price', input.productPrice.value.trim());
            form.append('image', input.productImage.files[0]);
            form.append('data_type', 'add_product');

            // Check other images
            if (input.productImage2.files.length > 0) {
                form.append('image2', input.productImage2.files[0]);
            }

            if (input.productImage3.files.length > 0) {
                form.append('image3', input.productImage3.files[0]);
            }

            if (input.productImage4.files.length > 0) {
                form.append('image4', input.productImage4.files[0]);
            }

            console.log(form, input.productName.value);

            // Send files throuh ajax
            send_data_files(url, form, handle_result);
        }
    }
};


// Collect edited data
const collect_edit_data = function (url, input, data_type, errMsg, handle_result, e) {

    if (data_type === 'edit_category') {
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
    }


    if (data_type === 'edit_product') {
        console.log(url);
        console.log(input);
        console.log(errMsg)


        console.log(url);

        // Loop through array of input and store values in variables

        console.log(input.productName, input.productQuantity, input.productCategory, input.productPrice, input.productImage, input.productId);
        let err = false;

        // Validate inputs
        if (input.productName.value.trim() === '' || !isNaN(input.productName.value)) {
            input.productName.classList.add('errInput')
            err = true;

        } else {
            input.productName.classList.remove('errInput');
        }

        if (input.productQuantity.value.trim() === '') {
            input.productQuantity.classList.add('errInput')
            err = true;

        } else {
            input.productQuantity.classList.remove('errInput');
        }

        if (input.productCategory.value.trim() === '') {
            input.productCategory.classList.add('errInput')
            err = true;

        } else {
            input.productCategory.classList.remove('errInput');
        }

        if (input.productPrice.value.trim() === '') {
            input.productPrice.classList.add('errInput')
            err = true;

        } else {
            input.productPrice.classList.remove('errInput');
        }

        console.log(input.productImage.src, input.productImage.files);


        console.log(url, err);
        if (!err) {
            const data = new FormData();
            // form data to be sent to post
            data.append('id', input.productId.dataset.rowid);
            data.append('description', input.productName.value.trim());
            data.append('quantity', input.productQuantity.value.trim());
            data.append('category', input.productCategory.value.trim());
            data.append('price', input.productPrice.value.trim());
            data.append('image', input.productImage.files[0]);
            data.append('data_type', data_type);

            // Check other images
            // console.log(input.productImage.dataset.url);
            // Check other images
            console.log(input.productImage.files);
            if (input.productImage.files.length > 0) {
                data.append('image', input.productImage.files[0]);
            } else {
                data.append('image', input.preview_image.src.substring(input.preview_image.dataset.url.length));
            }

            if (input.productImage2.files.length > 0) {
                data.append('image2', input.productImage2.files[0]);
            } else {
                data.append('image2', input.preview_image2.src.substring(input.preview_image2.dataset.url.length));
            }

            if (input.productImage3.files.length > 0) {
                data.append('image3', input.productImage3.files[0]);
            } else {
                data.append('image3', input.preview_image3.src.substring(input.preview_image3.dataset.url.length));
            }

            if (input.productImage4.files.length > 0) {
                data.append('image4', input.productImage4.files[0]);
            } else {
                data.append('image4', input.preview_image4.src.substring(input.preview_image4.dataset.url.length));
            }
            console.log(input);

            // Send files throuh ajax
            send_data_files(url, data, handle_result);
        }
    }
};





