import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {w3cwebsocket as W3CWebSocket} from 'websocket';
import DirectChat from "./DirectChat";
import WS from '../../../../vendor/gos/web-socket-bundle/Resources/public/js/gos_web_socket_client';

let el = document.getElementById('chat');
//const client = new W3CWebSocket('https://parentsolo.backndev.fr/ws');
// const eventSource = new EventSource('https://parentsolo.backndev.fr:3000?topic=' + encodeURIComponent('https://parentsolo.backndev.fr/ws'));
// eventSource.onmessage = event => {
//     // Will be called every time an update is published by the server
//     console.log(JSON.parse(event.data));
// }

export default class Chat extends Component {
    constructor(props) {
        super(props);
        this.state = {
            content: [],
            message: null
        };

        WS.connect('wss://parentsolo.backndev.fr:3000')
    }



    render() {
        return (
            <div>
                coucou
            </div>
        );
    }
}
ReactDOM.render(<Chat/>, document.getElementById('chat'));