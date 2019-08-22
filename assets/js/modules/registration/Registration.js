import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

export default class Registration extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: false,
            data: [],
            e: null
        }
    }

    componentDidMount(){
        let elem = document.getElementById('register');
        let token = elem.dataset.token;
        let data = {};
        data.token = token;
        axios.post('/api/register', data)
            .then(res => {
                if (res.data.data.error){
                    this.setState({
                        e: res.data.data.error
                    })
                }
                else {
                    this.setState({
                        isLoaded: true,
                        data: res.data.data
                    })
                }
            })
            .catch(e => {
                this.setState({
                    e: 'An error was return from the server'
                })
            })
    }

    render() {
        return (
            <div>
                hello
            </div>
        );
    }
}
ReactDOM.render(<Registration/>, document.getElementById('register'));
