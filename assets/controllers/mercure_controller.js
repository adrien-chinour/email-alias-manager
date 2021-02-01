import {Controller} from 'stimulus';
import * as Bootstrap from 'bootstrap';
import {Toaster} from "../utils/toaster";

export default class extends Controller {
    connect() {
        const hubUrl = new URL(this.element.dataset.hubUrl);
        hubUrl.searchParams.append('topic', 'notifications');

        const eventSource = new EventSource(hubUrl.toString());
        eventSource.onmessage = event => {
            const data = JSON.parse(event.data);
            const toast = Toaster.add(data.type, data.message);
            new Bootstrap.Toast(toast).show();
        }
    }
}
