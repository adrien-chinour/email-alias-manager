/** provide an easy way to add a new toast message with js **/

class Toaster {

    static add(backgroundColor, message) {
        let toastEl = document.getElementById('toast-prototype').cloneNode(true);

        // cleanup toast element
        toastEl.classList.remove('visually-hidden');
        toastEl.removeAttribute('id');

        // add toast property
        toastEl.classList.add('bg-' + backgroundColor);
        toastEl.getElementsByClassName('toast-body')[0].innerHTML = message;

        // add to DOM
        document.getElementById('toast-container').appendChild(toastEl);

        // return Toast instance of element
        return toastEl;
    }

}

export {Toaster}
