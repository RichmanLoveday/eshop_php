const addSlider = document.querySelector('.add_slider');
const headerTextOne = document.querySelector('.header_text_one');
const headerTextTwo = document.querySelector('.header_text_two');
const contentLink = document.querySelector('.content_link');
const mainMessage = document.querySelector('.main_message');
const slider_image = document.querySelector('.slider_image');
const summitSlider = document.querySelector('.summit_slider');
const modal = document.querySelector('.myModal');
const img = document.querySelector('.img_add');
const close_btn = document.querySelector('.close_modal');
const sliderDetails = document.querySelector('.sliders_details');


// error filed
const errorMessage = document.querySelector('.errorFild');


// open modal
function open_modal(modal, overlay) { // clear input
    clearInput();

    modal.classList.remove('hide');
    overlay.classList.remove('hide');
}
addSlider.addEventListener('click', open_modal.bind(this, modal, overlay));


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
const validate_input = async (e) => {
    const url = e.target.dataset.url;
    const message = "Please input this field";
    let err = false;

    console.log(headerTextOne.value.trim());

    // check data filled
    if (headerTextOne.value.trim() === '') { // errorMessage.classList.add('error');
        headerTextOne.classList.add('errInput');
        err = true;
    } else {
        headerTextOne.classList.remove('errInput');
    }

    if (headerTextTwo.value.trim() == '') {
        headerTextTwo.classList.add('errInput');
        err = true;

    } else {
        headerTextTwo.classList.remove('errInput');
    }

    if (contentLink.value.trim() == '') {
        contentLink.classList.add('errInput');
        err = true;

    } else {
        contentLink.classList.remove('errInput');

    }

    if (mainMessage.value == '') {
        mainMessage.classList.add('errInput');
        err = true;
    } else {
        mainMessage.classList.remove('errInput');
    }


    if (slider_image.files.length == 0) {
        slider_image.classList.add('errInput');
        err = true;

    } else if (slider_image.files[0].type != 'image/jpeg' && slider_image.files[0].type != 'image/jpg' && slider_image.files[0].type != 'image/png') {
        slider_image.classList.add('errInput');
        err = true;

    } else {
        err = false;
    }


    if (! err) { // send data to ajax
        const data = new FormData();
        data.append('header1_text', headerTextOne.value.trim());
        data.append('header2_text', headerTextTwo.value.trim());
        data.append('link', contentLink.value.trim());
        data.append('text', mainMessage.value.trim());
        data.append('image', slider_image.files[0]);

        const res = await axios.post(url, data, {"Content-Type": "multipart/form-data"});

        // console.log(data);
        console.log(res.data);


        // handle responses
        let slider = `<tr><td>${
            res.data.header1_text
        }</td><td>${
            res.data.header2_text
        }</td><td>${
            res.data.text
        }</td><td><a href="${
            res.data.link
        }">${
            res.data.link
        }</a></td><td><img src="${
            res.data.image
        }" style="width:50px; height:50px"></td><td>${
            res.data.status ? 'Yes' : 'No'
        }</td></tr>`;

        sliderDetails.insertAdjacentHTML('beforeend', slider)

    }


};
summitSlider.addEventListener('click', validate_input);


// display image
slider_image.addEventListener('change', function (e) {
    if (slider_image.files[0].type != 'image/jpg' && slider_image.files[0].type != 'image/jpeg' && slider_image.files[0].type != 'image/png') {
        slider_image.classList.add('errInput');
    } else {
        slider_image.classList.remove('errInput');

        // show image
        img.classList.remove('hide');
        img.src = URL.createObjectURL(slider_image.files[0]);
    }

})
