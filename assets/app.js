/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css'
import 'mdb-ui-kit';

// start the Stimulus application
import './bootstrap';

// Bootstrap 5
import * as Bootstrap from 'bootstrap'
import '@popperjs/core'

// Enable tooltips everywhere
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
const tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new Bootstrap.Tooltip(tooltipTriggerEl));

// Enable toasts everywhere
const toastElList = [].slice.call(document.querySelectorAll('.toast:not(#toast-prototype)'))
const toastList = toastElList.map((toastEl) => new Bootstrap.Toast(toastEl));
toastList.forEach(toast => toast.show());

// remove toast from DOM after hidden event
document.addEventListener('hidden.bs.toast', function (event) {
    event.target.remove();
})
