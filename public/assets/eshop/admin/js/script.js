// const axios = require('axios');
const btnAddCategory = document.querySelector('.btn_add_category');
const modal = document.querySelector('.myModal');
const editModal = document.querySelector('.edit_modal');
const success_modal = document.querySelector('.success_modal');
const modal_message = document.querySelector('.modal_message');
const overlay = document.querySelector('.overlay');
const btnCloseModal = document.querySelector('.close_modal');
const inputCategoryName = document.querySelector('.category_name');
const inputEditCategoryName = document.querySelector('.edit_category_name');
const sendData = document.querySelector('.sendData');
const form = document.querySelector('.form');
const tableBody = document.querySelector('.table_body');
const errorFiled = document.querySelector('.errorFild');
const rowDelete = document.querySelector('.row_delete');
const rowEdit = document.querySelector('.row_edit');

// console.log(btnAddCategory);

// Open modal and overlay
const show_modal = function (modal) {
    modal.classList.toggle('show');
    overlay.classList.toggle('show');

    // focus input
    inputCategoryName.focus();

    form.addEventListener('onsubmit', function (e) {
        console.log(e);
        e.preventDefault();
    });
};

// Close modal
const close_modal = function () {
    modal.classList.remove('show');
    editModal.classList.remove('show');
    overlay.classList.remove('show');
    success_modal.classList.remove('show');

    console.log(inputCategoryName);
    inputCategoryName.value = '';

    // clear error messageh
    errorFiled.classList.remove('error');
    errorFiled.textContent = '';
};


btnAddCategory.addEventListener('click', show_modal.bind(this, modal));
overlay.addEventListener('click', close_modal);
btnCloseModal.addEventListener('click', close_modal);


const handle_result = function (result) {
    if (result !== "") {
        const obj = result;
        console.log(obj);
        // console.log('Yes')
        if (obj.data_type === 'add_new') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    close_modal();
                    modal_message.textContent = obj.message;
                    console.log(modal_message);
                    console.log(modal_message.textContent);
                    show_modal(success_modal);

                    setTimeout(() => {
                        close_modal();
                    }, 5000);

                    // Add datas to the table
                    tableBody.innerHTML = obj.data;
                } else {
                    alert(obj.message);
                }
            }
        }

        if (obj.data_type === 'edit_row') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    close_modal();
                    alert(obj.message);

                    // Add datas to the table
                    tableBody.innerHTML = obj.data;
                } else {
                    alert(obj.message);
                }
            }
        }

        if (obj.data_type === 'delete_row') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    modal_message.textContent = obj.message;
                    console.log(modal_message);
                    console.log(modal_message.textContent);
                    show_modal(success_modal);

                    // Set timeout to close modal
                    setTimeout(() => {
                        close_modal();
                    }, 5000);

                    // Update table
                    tableBody.innerHTML = obj.data;
                } else {
                    alert(obj.message);
                }
            }
        }


        if (obj.data_type === 'disable_row') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    // alert(obj.message);

                    // Update table
                    tableBody.innerHTML = obj.data;
                } else {
                    alert(obj.message);
                }
            }
        }


        if (obj.data_type === 'edit_row') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    // alert(obj.message);

                    // Update table
                    tableBody.innerHTML = obj.data;
                } else {
                    alert(obj.message);
                }
            }
        }


        if (obj.data_type === 'edit_cat') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    modal_message.textContent = obj.message;
                    console.log(modal_message);
                    console.log(modal_message.textContent);
                    show_modal(success_modal);

                    // Set timeout to close modal
                    setTimeout(() => {
                        close_modal();
                    }, 5000);

                    // Update table
                    tableBody.innerHTML = obj.data;
                } else {
                    alert(obj.message);
                }
            }
        }

    }

};

const send_data = async function (url, data = {}) {
    const res = await axios.post(url, data, {
        "Content-Type": "multipart/form-data"
    });

    // console.log(res.data);

    handle_result(res.data);
};


const get_data = async function (url, data = {}) {
    const res = await axios.post(url, data, {
        "Content-Type": "multipart/form-data"
    });
    return res.data;
    // handle_result(res.data);
};

const collect_data = function (url, input_name, errMsg) {
    if (input_name.value.trim() === '' || !isNaN(input_name.value)) {
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
        const data = inputCategoryName.value.trim();

        send_data(url, { data: data, data_type: 'add_category' });
    }
};


// Collect edited data
const collect_edit_data = function (url, input_name, errMsg) {
    console.log(url);
    console.log(input_name);
    console.log(errMsg)
    if (input_name.value.trim() === '' || !isNaN(input_name.value)) {
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
        const data = inputEditCategoryName.value.trim();
        const id = inputEditCategoryName.dataset.rowid;

        send_data(url, { id: id, data: data, data_type: 'edit_category' });
    }
};


// Edit and delete Row
const edit_row = async function (url, id, e) {
    const data = await get_data(url, { id: id, data_type: 'get_cat_data' });
    console.log(data);
    console.log(data.input);
    inputEditCategoryName.value = data.input;
    console.log(inputEditCategoryName.value);
    inputEditCategoryName.dataset.rowid = id;
    console.log(inputEditCategoryName.dataset.rowid);
    show_modal(editModal);

};


const delete_row = function (url, state, e) {
    const rowId = e.target.dataset.rowid;
    const category_name = e.target.parentElement.parentElement.firstElementChild.textContent;

    if (!confirm("Are you sure you want to delete this row?")) return;

    // console.log(rowId);
    send_data(url, { id: rowId, category: category_name, data_type: 'delete_row' });
};


// Disbale or enable row
const disable_row = function (url, state, e) {
    const rowId = e.target.dataset.rowid;

    console.log(rowId);

    send_data(url, { id: +rowId, data_type: 'disable_row', current_state: state });
};


