import React, {Component} from 'react'
import axios from 'axios';
import Logger from "../../common/Logger";
import ReactDOM from 'react-dom';

export default class ResetEmail extends Component{
    constructor(props){
        super(props);
        let elem = document.getElementById('reset');
        this.state = {
            token: elem.dataset.csrf,
            email: null,
            message: null,
            type: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e){
        this.setState({
            email: e.target.value
        })
    }

    handleSubmit(){
        event.preventDefault();
        let data = {
            token: this.state.token,
            email: this.state.email,
        };
        axios.post('/reset', data)
            .then(res => {
                if (res.data.success){
                    this.setState({
                        type: 'success',
                        message: res.data.success
                    });
                    setTimeout(window.location.href = '/', 2000)
                }
                else {
                    this.setState({
                        type: 'error',
                        message: res.data.error
                    })
                }
            })
    }

    render() {
        const {type, message} = this.state;
        return (
            <div>
                <Logger type={type} message={message}/>
                <form onChange={this.handleChange} onSubmit={this.handleSubmit} method="post">
                    <div className="form-group">
                        <label htmlFor="email">Type your email</label>
                        <input type="email" name="email" className="form-control" required/>
                    </div>
                    <div className="marg-top-10">
                        <button className="btn btn-group-lg btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        );
    }
}
ReactDOM.render(<ResetEmail/>, document.getElementById('reset'));