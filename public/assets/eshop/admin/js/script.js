const btnAddCategory = document.querySelector('.btn_add_category');
const addNew = document.querySelector('.add_new');
const overlay = document.querySelector('.overlay');
const btnCloseModal = document.querySelector('.close_modal');
const inputCategoryName = document.querySelector('.category_name');
console.log(btnAddCategory);


// Open modal and overlay
btnAddCategory.addEventListener('click', function (e) {
    addNew.classList.toggle('show');
    overlay.classList.toggle('show');

    // focus input
    inputCategoryName.focus();

});

// Close modal
overlay.addEventListener('click', function () {
    addNew.classList.toggle('show')
    overlay.classList.toggle('show')

    console.log(inputCategoryName);
    inputCategoryName.value = '';
});

btnCloseModal.addEventListener('click', () => {
    addNew.classList.toggle('show')
    overlay.classList.toggle('show')
    inputCategoryName.value = '';
})

