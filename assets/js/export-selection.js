window.addEventListener('load', function () {
    document.getElementById('select-all-alias').addEventListener('click', function () {
        checker('checkbox-alias', document.getElementById('select-all-alias').checked)
    });
    document.getElementById('select-all-tags').addEventListener('click', function () {
        checker('checkbox-tags', document.getElementById('select-all-tags').checked)
    })
});

function checker(className, check) {
    let elements = document.getElementsByClassName(className);
    for (let i = 0; i < elements.length; i++) {
        check
            ? elements[i].setAttribute('checked', 'checked')
            : elements[i].removeAttribute('checked');
    }
}
