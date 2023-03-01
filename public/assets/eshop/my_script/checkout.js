const country = document.querySelector('.countryList');
const state = document.querySelector('.stateList');

const handle_check_out = function (result) {
    console.log(result)
    const obj = result;
    if (obj !== '') {
        if (obj.data_type === 'get_states') {
            state.innerHTML = '<option>-- State / Province / Region --</option>'
            obj.data.forEach(st => {
                state.innerHTML += `<option value='${st.id}'>${st.state}</option>`;
            });
            console.log(state.innerHTML);
        }
    }
}

const get_states = function (e) {
    //e.preventDefault();
    console.log(e.target);
    const id = e.target.value;
    const url = e.target.dataset.url;
    console.log(id, url);

    // ajax data to php
    send_data(url, { id: id, data_type: 'get_states' }, handle_check_out)
}
country?.addEventListener('input', get_states);


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


