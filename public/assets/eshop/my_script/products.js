const btnAddProduct = document.querySelector('.btn_add_Product');
const btnAddNewProduct = document.querySelector('.addNewProduct') || null;
const editProductModal = document.querySelector('.edit_product_modal');
const btnEditProduct = document.querySelector('.editProduct') || null;
const btnDeleteProduct = document.querySelector('.deleteProduct') || null;
const inputProductName = document.querySelector('.product_name');
const productModal = document.querySelector('.product_modal') || null;
const imagesEdit = document.querySelectorAll('.img_edit');
const imagesAdd = document.querySelectorAll('.img_add');


// INPUT DETAILS
const input = {
    productName: document.querySelector('.product_name'),
    productQuantity: document.querySelector('.quantity'),
    productCategory: document.querySelector('.category'),
    productPrice: document.querySelector('.price'),
    productImage: document.querySelector('.image'),
    productImage2: document.querySelector('.image2'),
    productImage3: document.querySelector('.image3'),
    productImage4: document.querySelector('.image4'),
    imagesAdd: imagesAdd
};


// Edit product datas
const editInput = {
    productId: document.querySelector('.rowId'),
    productName: document.querySelector('.edit_product_name'),
    productQuantity: document.querySelector('.edit_quantity'),
    productCategory: document.querySelector('.edit_category'),
    productPrice: document.querySelector('.edit_price'),
    productImage: document.querySelector('.edit_image'),
    productImage2: document.querySelector('.edit_image2'),
    productImage3: document.querySelector('.edit_image3'),
    productImage4: document.querySelector('.edit_image4'),


    // preview images
    preview_image: document.querySelector('.preview_image'),
    preview_image2: document.querySelector('.preview_image2'),
    preview_image3: document.querySelector('.preview_image3'),
    preview_image4: document.querySelector('.preview_image4'),

    // imagesEdit: imagesEdit,
};

const display_image = function (target, nodelist) {
    console.log(target);
    console.log(nodelist)
    // Number to target array nodelist
    let number;
    if (target.name === 'image') {
        number = 0;
    }
    if (target.name === 'image2') {
        number = 1;
    }
    if (target.name === 'image3') {
        number = 2;
    }
    if (target.name === 'image4') {
        number = 3;
    }

    // Listen to event
    target.addEventListener('change', function (e) { // set the preview images
        console.log(target.files);
        nodelist[number].classList.remove('hide');
        nodelist[number].src = URL.createObjectURL(target.files[0]);
    });
}

btnAddProduct ?. addEventListener('click', show_modal.bind(this, productModal, input));

// btnCloseModal?.addEventListener('click', close_modal.bind(this, productModal, overlay, inputCategoryName));

handle_result = function (result) {
    console.log(result);
    if (result !== "") {
        const obj = result;
        if (obj.data_type === 'add_new') {
            if (typeof obj.message_type !== 'undefined') {
                console.log(obj);
                if (obj.message_type === 'info') {
                    close_modal(productModal, overlay, input)
                    modal_message.textContent = obj.message;
                    console.log(modal_message);
                    console.log(modal_message.textContent);
                    show_modal(success_modal);

                    // Add datas to the table
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

        if (obj.data_type === 'delete_product') {
            if (typeof obj.message_type !== 'undefined') {
                if (obj.message_type === 'info') {
                    Swal.fire({title: 'Deleted!', customClass: 'swal', icon: 'success', text: obj.message})
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
                    // console.log(obj.data)
                    // Update table
                    tableBody.innerHTML = obj.data;
                } else {
                    alert(obj.message);
                }
            }
        }


        if (obj.data_type === 'edit_product') {
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

                    // Update table
                    // console.log(data);
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

// Add new product
btnAddNewProduct ?. addEventListener('click', collect_data.bind(this, input, 'Please enter a value here', 'add_product', handle_result));


// Edit and delete Row

const edit_product_row = async function (e) { // console.log(e);
    if (! e.target.classList.contains('editProduct')) 
        return;
    


    console.log(e.target.dataset);
    const id = e.target.dataset.rowid;
    const url = e.target.dataset.rowurl;
    console.log(typeof url, url);
    const data = await get_data(url, {
        id: id,
        type: 'get',
        data_type: 'get_product_data'
    });

    editInput.data = data;
    // add data to editInput object

    // show modal of edit productModal
    show_modal(editProductModal, editInput);

    // Add event listener to product images
    editProductModal.addEventListener('click', function (e) {

        if (! e.target.classList.contains('edit')) 
            return;
        


        const image = e.target; // store click image element
        console.log(image.name);

        // display image on field elements
        display_image(image, imagesEdit);

    });

    // Even listener for submit btn
    btnEditProduct ?. addEventListener('click', collect_edit_data.bind(this, url, editInput, 'edit_product', 'Please input here', handle_result));
};
tableBody.addEventListener('click', edit_product_row);


const delete_product_row = function (e) {
    if (! e.target.classList.contains('deleteProduct')) 
        return;
    


    console.log(e.target);

    const url = e.target.dataset.rowurl;
    const id = e.target.dataset.rowid;
    console.log(url);
    console.log(Swal);
    Swal.fire({
        title: 'Are you sure?',
        customClass: 'swal',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) { // data sent to php
            send_data(url, {
                id: id,
                data_type: 'delete_product'
            }, handle_result);
        }
    })
}
tableBody.addEventListener('click', delete_product_row);
