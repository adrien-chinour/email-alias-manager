import {Controller} from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        window.addEventListener('load', function () {
            document.getElementById('select-all-alias').addEventListener('click', function () {
                checker('checkbox-alias', document.getElementById('select-all-alias').checked)
            });
        });

        function checker(className, check) {
            let elements = document.getElementsByClassName(className);
            for (let i = 0; i < elements.length; i++) {
                check
                    ? elements[i].setAttribute('checked', 'checked')
                    : elements[i].removeAttribute('checked');
            }
        }
    }


}
