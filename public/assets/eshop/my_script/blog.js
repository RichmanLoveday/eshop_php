const addPost = document.querySelector('.add_post');
const postTitle = document.querySelector('.post_title');
const postImage = document.querySelector('.post_image');
const blogText = document.querySelector('.blog_text');
const summitBlog = document.querySelector('.summit_blog');
const blogDetails = document.querySelector('.message_items');


// open modal
function open_modal(modal, overlay) {
    // clear input
    // clearInput();
    console.log('yessss');

    modal.classList.remove('hide');
    overlay.classList.remove('hide');
}
addPost.addEventListener('click', open_modal.bind(this, modal, overlay));


// clear inpuit
function clearInput() {
    document.querySelectorAll('.input').forEach(el => {
        el.value = '';
        el.classList.remove('errInput');
    });

    // clear img
    img.classList.add('hide');

}

// function close modal
function hide_modal(modal, overlay) { // clear input
    clearInput();

    modal.classList.add('hide');
    overlay.classList.add('hide');
}

overlay.addEventListener('click', hide_modal.bind(this, modal, overlay));
close_btn.addEventListener('click', hide_modal.bind(this, modal, overlay));


// validate input
const validate_post = async (e) => {
    const url = e.target.dataset.url;
    const message = "Please input in this field";
    let err = false;
    console.log(url);

    // check data filled
    if (postTitle.value.trim() === '') { // errorMessage.classList.add('error');
        postTitle.classList.add('errInput');
        err = true;
    } else {
        postTitle.classList.remove('errInput');
    }

    if (blogText.value.trim() === '') {
        blogText.classList.add('errInput');
        err = true;
    } else {
        blogText.classList.remove('errInput');
    }


    if (postImage.files.length == 0) {
        postImage.classList.add('errInput');
        err = true;

    } else if (postImage.files[0].type != 'image/jpeg' && postImage.files[0].type != 'image/jpg' && postImage.files[0].type != 'image/png') {
        postImage.classList.add('errInput');
        err = true;
    }


    console.log(err);
    if (! err) { // send data to ajax
        const data = new FormData();
        data.append('title', postTitle.value.trim());
        data.append('post', blogText.value.trim());
        data.append('image', postImage.files[0]);

        const res = await axios.post(url, data, {"Content-Type": "multipart/form-data"});

        // console.log(data);
        console.log(res.data);

        // handle responses

        if (! res.data.error) {
            let blog = `<tr><td>${
                res.data.title
            }</td><td>${
                res.data.owner
            }</td><td>${
                res.data.post
            }</td><td><img src="${
                res.data.image
            }" style="width:60px; height:60px"></td><td>${
                res.data.date
            }</td><td>
            <div>
                <button data-id="${
                res.data.id
            }" data-url="${
                res.data.url
            }ajax_blog/edit" class="btn btn-warning btn-sm edit_blog" style="outline: none;">
                    <i class="fa fa-pencil" style="margin-right:5px;"></i>
                    Edit
                </button>
                <button data-id="${
                res.data.id
            }" data-url="${
                res.data.url
            }ajax_blog/delete"class="btn btn-danger btn-sm delete_blog" style="outline:none;"><i class="fa fa-trash-o" style="margin-right:5px;"></i>Delete</button>
            </div>
            </td></tr>`;

            blogDetails.insertAdjacentHTML('beforeend', blog);

            // slear modal
            hide_modal(modal, overlay);

            // display alert
            Swal.fire({
                icon: 'success',
                title: res.data.message,
                showConfirmButton: false,
                customClass: {
                    popup: 'swal'
                }
            })

            // close sweet alert
            const timeout = setTimeout(() => {
                swal.close();
            }, 1500);


        }
    }


};
summitBlog.addEventListener('click', validate_post);


// // display image
postImage.addEventListener('change', function (e) {
    if (postImage.files[0].type != 'image/jpg' && postImage.files[0].type != 'image/jpeg' && postImage.files[0].type != 'image/png') {
        postImage.classList.add('errInput');
    } else {
        postImage.classList.remove('errInput');

        // show image
        img.classList.remove('hide');
        img.src = URL.createObjectURL(postImage.files[0]);
    }

})
