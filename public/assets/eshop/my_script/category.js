const btnAddCategory = document.querySelector('.btn_add_category') || null;
const editModal = document.querySelector('.edit_category');
const inputCategoryName = document.querySelector('.category_name');
const inputEditCategoryName = document.querySelector('.edit_category_name');
const btnAddNewCategory = document.querySelector('.addNewCategory') || null;
const btnEditCategory = document.querySelector('.editCategory') || null;
const disableRow = document.querySelectorAll('.disable_row');
const catModal = document.querySelector('.myModal');
const parentSelect = document.querySelector('.parent');
const parent_edit = document.querySelector('.parent_edit');


// Handle modal open and closing
btnAddCategory?.addEventListener('click', show_modal.bind(this, catModal, inputCategoryName));
//overlay.addEventListener('click', close_modal.bind(this, modal, overlay, inputCategoryName));
// btnCloseModal?.addEventListener('click', close_modal.bind(this, modal, overlay, inputCategoryName));



// Handle result function
handle_result = function (result) {
    if (result !== "") {
        const obj = result;
        console.log(obj);
        // console.log('Yes')
        if (obj.data_type === 'add_new') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    close_modal(catModal, overlay, inputCategoryName)
                    modal_message.textContent = obj.message;
                    console.log(modal_message);
                    console.log(modal_message.textContent);
                    show_modal(success_modal);

                    // Add datas to the table
                    tableBody.innerHTML = obj.data;

                    // Set timeout to close modal
                    setTimeout(() => {
                        close_modal(success_modal, editModal);
                    }, 5000);


                } else {
                    alert(obj.message);
                }
            }
        }

        if (obj.data_type === 'edit_row') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    close_modal(editModal, overlay, inputEditCategoryName);
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

                    // Update table
                    tableBody.innerHTML = obj.data;

                    // Set timeout to close modal
                    setTimeout(() => {
                        close_modal(success_modal, overlay);
                    }, 5000);

                } else {
                    alert(obj.message);
                }
            }
        }


        if (obj.data_type === 'disable_row') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    tableBody.innerHTML = obj.data;         // Update table
                    parentSelect.innerHTML = obj.parent;    // Update select option
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

                    close_modal(editModal, overlay);
                    show_modal(success_modal);

                    // Update table
                    tableBody.innerHTML = obj.data;

                    // Set timeout to close modal
                    setTimeout(() => {
                        close_modal(success_modal, overlay);
                    }, 5000);
                } else {
                    alert(obj.message);
                }
            }
        }

    }

};

// Event listener to add new category
btnAddNewCategory?.addEventListener('click',
    collect_data
        .bind(
            this, { category: inputCategoryName, parent: parentSelect }, 'Please enter a valid category name', 'add_category', handle_result
        ));



// Disbale or enable row
const disable_row = function (e) {
    if (!e.target.classList.contains('disable_row')) return;

    console.log(e.target.dataset);
    const rowId = e.target.dataset.rowid;
    const url = e.target.dataset.rowurl;
    const state = e.target.dataset.rowstate;

    console.log(e.target);
    send_data(url, { id: +rowId, data_type: 'disable_row', current_state: state }, handle_result);
};
// Loop through to add event listener
// console.log(disableRow);
tableBody.addEventListener('click', disable_row);


// Edit and delete Row
const edit_row = async function (e) {
    if (!e.target.classList.contains('row_edit')) return;

    console.log(e.target.dataset);
    const id = e.target.dataset.rowid;
    const url = e.target.dataset.rowurl;
    console.log(typeof url, url);
    const data = await get_data(url, { id: id, data_type: 'get_cat_data' });

    console.log(data);
    console.log(data.input);

    Object.values(parent_edit.options).forEach(option => {
        (option.value === data.parent) ? option.selected = true : option.selected = false;
    });
    inputEditCategoryName.value = data.input
    console.log(inputEditCategoryName.value);
    inputEditCategoryName.dataset.rowid = id;
    console.log(inputEditCategoryName.dataset.rowid);
    show_modal(editModal, inputEditCategoryName);

    // Even listener for submit btn
    btnEditCategory?.addEventListener('click', collect_edit_data.bind(this, url, { category_edit: inputEditCategoryName, parent_edit: parent_edit }, 'edit_category', 'Please input a correct catgory', handle_result));

};
tableBody.addEventListener('click', edit_row);


// Delete Catgory function
const delete_row = function (e) {
    if (!e.target.classList.contains('row_delete')) return;

    console.log(e.target.dataset);
    const id = e.target.dataset.rowid;
    const url = e.target.dataset.rowurl;
    const catname = e.target.dataset.catname;
    console.log(typeof url, url);

    if (!confirm("Are you sure you want to delete this row?")) return;

    // console.log(rowId);
    send_data(url, { id: id, category: catname, data_type: 'delete_row' }, handle_result);
};
tableBody.addEventListener('click', delete_row);



// collect_edit_data('<?=ROOT?>ajax', inputEditCategoryName, 'Please enter a valid category name')


