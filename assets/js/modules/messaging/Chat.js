import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {w3cwebsocket as W3CWebSocket} from 'websocket';
import DirectChat from "./DirectChat";

let el = document.getElementById('chat');
//const client = new W3CWebSocket('https://parentsolo.backndev.fr/ws');
const eventSource = new EventSource('https://parentsolo.backndev.fr:3000?topic=' + encodeURIComponent('https://parentsolo.backndev.fr/ws'));
eventSource.onmessage = event => {
    // Will be called every time an update is published by the server
    console.log(JSON.parse(event.data));
}

export default class Chat extends Component {
    constructor(props) {
        super(props);
        this.state = {
            content: [],
            message: null
        };
    }

//         client.onopen = function () {
//         };
//         client.onmessage = (message) => {
//             this.setState({
//                 content: JSON.parse(message.data)
//             })
//         };
//         this.submitMessage = this.submitMessage.bind(this);
//         this.markAsRead = this.markAsRead.bind(this);
//     }
//
//     submitMessage(data) {
//         client.send(JSON.stringify(data));
//     }
//
//     setData(){
//         let properties = {};
//         this.state.content.map((c) => {
//             if (typeof properties[c.message_to] === 'undefined'){
//                 properties[c.message_to] = {message : []};
//             }
//             properties[c.message_to].message.push({message : decodeURI(c.content), isRead: c.is_read, from: c.message_from});
//         });
//         return properties
//     }
//
//     markAsRead(data){
//         client.send(JSON.stringify(data));
//     }
//
//     render() {
//         const {content} = this.state;
//         //console.log(content);
//         if (content.length > 0) {
//             let properties = this.setData();
//             return(
//                 <div>{
//                     Object.entries(properties).map((value, key) => {
//                         return (
//                             <div key={key} className="position-relative">
//                                 <DirectChat submitMessage={this.submitMessage} content={value} target={value[0]} user={el.dataset.user} markAsRead={this.markAsRead}/>
//                             </div>
//                         );
//                     })
//                 }</div>
//             );
//         }
//         else {
//             return(
//                 <div></div>
//             )
//         }
//     }
//
// }

    render() {
        return (
            <div>

            </div>
        );
    }
}
ReactDOM.render(<Chat/>, document.getElementById('chat'));