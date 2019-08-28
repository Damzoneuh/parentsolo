import React, {Component} from 'react';
import axios from 'axios';
import Logger from "../../common/Logger";
import ReactDOM from 'react-dom';

export default class Reset extends Component{
    constructor(props){
        super(props);
        let elem = document.getElementById('reset');
        this.state = {
            password: null,
            plainPassword: null,
            type: null,
            message: null,
            resetToken: elem.dataset.token,
            token: elem.dataset.csrf
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e){
        this.setState({
            [e.target.name] : e.target.value
        })
    }

    handleSubmit(){
        event.preventDefault();
        if (this.state.password === this.state.plainPassword) {
            let data = {
                password: this.state.password,
                plainPassword: this.state.plainPassword,
                token: this.state.token,
                resetToken: this.state.resetToken
            };
            axios.put('/api/reset', data)
                .then(res => {
                    if (res.data.success) {
                        this.setState({
                            type: 'success',
                            message: res.data.success
                        });
                        setTimeout(() => window.location.href = '/login', 2000)
                    } else {
                        this.setState({
                            type: 'error',
                            message: 'an error as been throw during the update'
                        })
                    }
                })
        }
        else {
            this.setState({
                type: 'error',
                message: 'Passwords not match . '
            })
        }
    }

    render() {
        const {type, message} = this.state;
        return (
            <div>
                <Logger type={type} message={message}/>
                <form onChange={this.handleChange} onSubmit={this.handleSubmit} method="post">
                    <div className="form-group">
                        <label htmlFor="password">Type a password</label>
                        <input type="password" name="password" className="form-control" required/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="plainPassword">Confirm your password</label>
                        <input type="password" name="plainPassword" className="form-control" required/>
                    </div>
                    <div className="marg-top-10">
                        <button className="btn btn-group-lg btn-primary">Register</button>
                    </div>
                </form>
            </div>
        );
    }
}

ReactDOM.render(<Reset/>, document.getElementById('reset'));