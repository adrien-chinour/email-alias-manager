import {Controller} from 'stimulus';
import * as Bootstrap from 'bootstrap';
import {Toaster} from "../utils/toaster";

export default class extends Controller {
    connect() {
        const hubUrl = new URL('http://localhost:1337/.well-known/mercure');
        hubUrl.searchParams.append('topic', 'notifications');

        const lastEventId = localStorage.getItem('lastEventId');
        if (lastEventId != null) {
            hubUrl.searchParams.append('Last-Event-ID', lastEventId);
        }

        const eventSource = new EventSource(hubUrl);
        eventSource.onmessage = event => {
            localStorage.setItem('lastEventId', event.lastEventId);
            const data = JSON.parse(event.data);
            const toast = Toaster.add(data.type, data.message);
            new Bootstrap.Toast(toast).show();
        }
    }
}
