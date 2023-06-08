const addPost = document.querySelector('.add_post');
const postTitle = document.querySelector('.post_title');
const blogText = document.querySelector('.blog_text');
const summitBlog = document.querySelector('.summit_blog');
const blogDetails = document.querySelector('.message_items');
const editBlogBtn = document.querySelector('.edit_blog');
const deleteBlogBtn = document.querySelector('.delete_blog');
const imageView = document.querySelector('.img_view');
const submitEditBlog = document.querySelector('.summit_edit_blog');
const postImage = document.querySelector('.post_image');

let editImage;
let state;


// open modal
function open_modal(modal, overlay) {
    // clear input
    // clearInput();
    console.log('yessss');

    modal.classList.remove('hide');
    overlay.classList.remove('hide');
}

function add_post(modal, overlay) {

    summitBlog.classList.remove('hide');
    submitEditBlog.classList.add('hide');

    open_modal(modal, overlay);
}
addPost.addEventListener('click', add_post.bind(this, modal, overlay));


// clear inpuit
function clearInput() {
    document.querySelectorAll('.input').forEach(el => {
        el.value = '';
        el.classList.remove('errInput');
    });

    // clear img
    imageView.classList.add('hide');

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
const validate_post = async (type, e) => {
    console.log(e);
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


    if (type == 'newPost') {
        if (postImage.files.length == 0) {
            postImage.classList.add('errInput');
            err = true;

        } else if (postImage.files[0].type != 'image/jpeg' && postImage.files[0].type != 'image/jpg' && postImage.files[0].type != 'image/png') {
            postImage.classList.add('errInput');
            err = true;
        }
    }

    // handle for edit post
    if (type == 'editPost') {
        if (postImage.files.length == 0) {
            editImage = imageView.src.substring(imageView.dataset.url.length);
            console.log(editImage);
        } else if (postImage.files[0].type != 'image/jpeg' && postImage.files[0].type != 'image/jpg' && postImage.files[0].type != 'image/png') {
            postImage.classList.add('errInput');
            err = true;
        }
    }


    console.log(err);
    if (! err) { // send data to ajax
        const data = new FormData();
        data.append('title', postTitle.value.trim());
        data.append('post', blogText.value.trim());

        if (editImage) {
            data.append('image', editImage);
        } else {
            data.append('image', postImage.files[0]);
        }

        if (type == 'editPost') {
            data.append('id', e.target.dataset.id);
            data.append('type', 'edit_blog');
        }


        const res = await axios.post(url, data, {"Content-Type": "multipart/form-data"});

        // console.log(data);
        console.log(res.data);

        // handle responses
        if (! res.data.status) {
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

            if (type == 'newPost') {
                blogDetails.insertAdjacentHTML('afterbegin', blog);
            }

            // change the html of the parentNode of the state
            if (type == 'editPost') {
                state.parentNode.parentNode.parentNode.innerHTML = blog;
            }
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
summitBlog.addEventListener('click', validate_post.bind(this, 'newPost'));


// // display image
postImage.addEventListener('change', function (e) {
    if (postImage.files[0].type != 'image/jpg' && postImage.files[0].type != 'image/jpeg' && postImage.files[0].type != 'image/png') {
        postImage.classList.add('errInput');
    } else {
        postImage.classList.remove('errInput');

        // show image
        imageView.classList.remove('hide');
        imageView.src = URL.createObjectURL(postImage.files[0]);
    }

})


// edit blog
async function edit_blog(modal, overlay, e) {
    if (! e.target.classList.contains('edit_blog')) 
        return;
    


    // get url
    // get id
    state = e.target; // save state of edit button
    const url = e.target.dataset.url;
    const id = e.target.dataset.id;
    console.log(url, id);

    // send and receive data from ajax
    const res = await axios.post(url, {
        id: id,
        type: 'get_data'
    }, {"Content-Type": "multipart/form-data"});

    console.log(res.data);
    if (res.data.status) { // display result

        console.log(res.data);

        imageView.classList.remove('hide');
        imageView.src = res.data.image;
        postTitle.value = res.data.title;
        blogText.value = res.data.post;

        // hide buttons
        summitBlog.classList.add('hide');
        submitEditBlog.classList.remove('hide');

        // ADD ID to button
        submitEditBlog.dataset.id = id;

        // open modal
        open_modal(modal, overlay);

    }
    // display result

}blogDetails.addEventListener('click', edit_blog.bind(this, modal, overlay));
submitEditBlog.addEventListener('click', validate_post.bind(this, 'editPost'));


// delete blog
function delete_blog(e) {
    if (! e.target.classList.contains('delete_blog')) 
        return;
    


    console.log(e);
    const url = e.target.dataset.url;
    const id = e.target.dataset.id;

    // popup confirmation modal
    Swal.fire({
        title: 'Are you sure?',
        text: "Changes won't be gotten back",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Delete',
        customClass: {
            popup: 'swal'
        }


    }).then((result) => {

        async function test() {
            const res = await axios.post(url, {
                id: id
            }, {"Content-Type": "multipart/form-data"});
            console.log(res.data);

            if (res.data.status) { // remove roll message
                e.target.parentNode.parentNode.parentNode.remove();

                Swal.fire({
                    icon: 'success',
                    title: res.data.message,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'swal'
                    }
                });

                // close sweet alert
                const timeout = setTimeout(() => {
                    swal.close();
                }, 1500);
            }
        }

        if (result.isConfirmed) {
            test();
        }

    })


    // send data to ajax

    // remove node on success


    console.log(url, id)
}
blogDetails.addEventListener('click', delete_blog);
