const select = document.querySelector('#email-form > select');

updateDomain(select.value);
select.addEventListener('change', () => {
    updateDomain(select.value);
})

function updateDomain(email) {
    document.getElementById('input-append-alias').innerHTML = '<strong>@' + email.split('@')[1] + '</strong>';
}
