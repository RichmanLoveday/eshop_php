const deleteMsg = document.querySelector('.delete_msg');
const messageItems = document.querySelector('.message_items');


function delete_msg(e) { // return if delete button is not clicked
    if (! e.target.classList.contains('delete_msg')) 
        return;
    


    const msgId = e.target.dataset.msgId;
    const url = e.target.dataset.url;
    console.log(url);

    // pop up delte confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Delete',
        customClass: {
            popup: 'swal'
        }
    }).then((result) => { // send message to ajax

        async function test() {
            const msgRes = await axios.post(url, {
                id: msgId
            }, {"Content-Type": "multipart/form-data"});
            console.log(msgRes.data);

            if (msgRes.data.success) { // remove roll message
                e.target.parentNode.parentNode.remove();

                Swal.fire({
                    icon: 'success',
                    title: msgRes.data.message,
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

        if (result.isConfirmed) {
            test();
        }


    })


    // return responce


}messageItems.addEventListener('click', delete_msg);
