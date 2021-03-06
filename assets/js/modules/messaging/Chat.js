import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import ChatBox from "../../common/ChatBox";


let el = document.getElementById('chat');
const es = new WebSocket('ws://ws.disons-demain.be:5000/c/' + el.dataset.user);

export default class Chat extends Component {
    constructor(props) {
        super(props);
        this.state = {
            content: [],
            messages: [],
            isGranted: false,
            user: el.dataset.user
        };
        axios.get('/api/user')
            .then(res => {
                if (res.data.isSub){
                    this.setState({
                        isGranted: true,
                    });
                }
            });

        this.setOrderedMessages = this.setOrderedMessages.bind(this);
        this.handlePayLoad = this.handlePayLoad.bind(this);
        this.setContent = this.setContent.bind(this);
    }

    componentDidMount(){
        es.onopen = () => {
            console.log('connected');
        };

        es.onmessage = res => {
            console.log(JSON.parse(res.data));
            let data = JSON.parse(res.data);
            if (data.length > 0 && data[0] !== 'no messages'){
                this.setState({
                    messages: data
                });
                console.log(data)
            }
            this.setOrderedMessages()
        };

        es.onerror = () => {
            es.onclose()
        };

        es.onclose = () => {
            es.onopen()
        }
    }

    setOrderedMessages(){
        let order = {};
        if (this.state.messages.length > 0){
            this.state.messages.map(message => {
                if (message.message_from === parseInt(el.dataset.user)){
                    if (typeof order[message.message_to] === 'undefined'){
                        order[message.message_to] = [message]
                    }
                    else{
                        order[message.message_to].push(message)
                    }
                }
                else {
                    if (typeof order[message.message_from] === 'undefined'){
                        order[message.message_from] = [message]
                    }
                    else{
                        order[message.message_from].push(message)
                    }
                }
            });
            this.setContent(order);
        }
    }

    setContent(order){
        this.setState({
            content: order
        });
    }

    handlePayLoad(payLoad){
        es.send(payLoad);
    }

    render() {
        const {isGranted, messages, content, user} = this.state;

        if (isGranted && Object.entries(content).length > 0){
            return (
                <div className="d-flex flex-row justify-content-around align-items-end position-fixed bottom-0 w-100 chat-wrap">
                    {Object.entries(content).map(message => {
                        return (
                            <ChatBox messages={message[1]} from={message[0]} handlePayLoad={this.handlePayLoad}/>
                        )
                    })}
                </div>
            );
        }
        else {
            return (
                <div> </div>
            )
        }
    }
}
ReactDOM.render(<Chat/>, document.getElementById('chat'));