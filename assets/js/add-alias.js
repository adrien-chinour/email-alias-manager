// load select2
import '../../node_modules/select2/dist/css/select2.min.css'
import '../../node_modules/select2/dist/js/select2.full.min'

const inputAppendId = "input-append-alias"; // custom id set on page template
const emailSelectId = "email_target"; // this id is define automatically from App\Form\EmailType

window.addEventListener("load", function () {
    init();
});

/**
 * Update alias input with domain completion and random name
 */
function updateAliasInput() {
    const selectEmailInput = document.getElementById(emailSelectId);

    if (selectEmailInput === null || selectEmailInput.selectedIndex === null) {
        console.error("No selected email.");
        return;
    }

    let email = selectEmailInput.options[selectEmailInput.selectedIndex].value;
    email = email.split("@");
    document.getElementById(inputAppendId).innerText = '@' + email[1];
}

/**
 * Init after page load
 */
function init() {
    updateAliasInput();
    document.getElementById(emailSelectId).addEventListener("change", updateAliasInput);

    $(document).ready(function () {
        $('.select2').select2({
            allowClear: true,
            tags: true
        });
    });
}
