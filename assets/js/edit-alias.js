// load select2
import '../../node_modules/select2/dist/css/select2.min.css'
import '../../node_modules/select2/dist/js/select2.full.min'


$(document).ready(function () {
    const input = $('.select2');

    input.select2({
        allowClear: true,
        tags: true
    });

    let tags = JSON.parse(document.getElementById('select-tags-input').dataset.tags);
    for (let i = 0; i < tags.length; i++) {
        let option = new Option(tags[i], tags[i], true, true);
        input.append(option).trigger('change');
    }
});
