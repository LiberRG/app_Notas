window.onload = function () {
    let eliminar = document.querySelectorAll('[name=eliminarNota]')
    eliminar.forEach(element => {
        element.onclick = function (event) {
            console.log(event.target.id)
            //evitamos que se envíe el formulario
            event.preventDefault();
            showModal('spa_modal', 'Confirmación',
                '¿Está seguro/a de que desea eliminar la nota?',
                null, null, ()=>{deleteNota2(event.target.id)}, null);
        };
    });
    console.log(eliminar);
}

const base_url = "http://127.0.0.1:8000/";
const OK_TEXT = "Aceptar";
const CANCEL_TEXT = "Cancelar";

function showModal(modal_id, title, msg,
    opt_ok_text = null,
    opt_cancel_text = null,
    opt_ok_function = null,
    opt_cancel_function = null) {
    let myModal = new bootstrap.Modal(document.getElementById(modal_id), { backdrop: 'static', keyboard: true, focus: true });

    let modal_id_selector = '#' + modal_id;

    let title_el = document.querySelector(modal_id_selector + ' #modal_title');
    let msg_el = document.querySelector(modal_id_selector + '  #modal_msg');
    let optok_el = document.querySelector(modal_id_selector + '  #opt_ok');
    let optcancel_el = document.querySelector(modal_id_selector + '  #opt_cancel');

    title_el.innerHTML = title;
    msg_el.innerHTML = msg;


    if (opt_ok_text !== null) {
        optok_el.innerHTML = opt_ok_text;
    } else {
        optok_el.innerHTML = OK_TEXT;
    }

    if (opt_cancel_text !== null) {
        optcancel_el.innerHTML = opt_cancel_text;
    } else {
        optcancel_el.innerHTML = CANCEL_TEXT;
    }

    let myModalEl = document.getElementById(modal_id);


    optok_el.onclick = function () {
        ok_clicked = true;
        cancel_clicked = false;

        myModalEl.addEventListener('hidden.bs.modal', function (event) {

            if (opt_ok_function !== null) {
                opt_ok_function();
            }

        }, { once: true });
        myModal.hide();
    };
    optcancel_el.onclick = function () {

        myModalEl.addEventListener('hidden.bs.modal', function (event) {

            if (opt_cancel_function !== null) {
                opt_cancel_function();
            }

        }, { once: true });
        myModal.hide();
    };
    myModalEl.addEventListener('shown.bs.modal', function () {
        optok_el.focus();
    }, { once: true });
    myModal.show();

}

function deleteNota(id) {
    let delete_url = "nota/delete/" + id;
    location.href = base_url + delete_url
}

function deleteNota2(id) {
    let delete_url = "nota/delete";
    const data = {'id': id};
    const request = new Request(base_url + delete_url, {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    });

    fetch(request)
        .then((response) => {
            console.log(response)
            if (response.status === 200) {
                console.log("eliminado")
                // location.reload();
            } else {
                console.log("Something went wrong on API server!");
                return false;
            }
        })
        .catch((error) => {
            console.error('Ha ocurrido un error ' + error);
        });
}