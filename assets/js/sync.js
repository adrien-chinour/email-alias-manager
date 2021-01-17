/** custom js for sync_index page **/

function xhrCall(event) {
    const request = new XMLHttpRequest();
    request.open('GET', event.target.dataset.url, true);
    request.send();
}

document.getElementById('btn-change').addEventListener('click', xhrCall)
document.getElementById('btn-syncing').addEventListener('click', xhrCall)
