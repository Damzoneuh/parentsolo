import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Logger from "../../common/Logger";

export default class Registration extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: true,
            data: [],
            number: null,
            email: null,
            password: null,
            plainPassword: null,
            type: null,
            message: null,
            reset: null,
            isMan: false
        };
        this.handleForm = this.handleForm.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleCheckbox = this.handleCheckbox.bind(this);
    }

    handleForm(e){
        if (e.target.type !== 'checkbox') {
            this.setState({
                [e.target.name]: e.target.value
            })
        }
    }

    handleSubmit(){
        event.preventDefault();
        if (
            this.state.password && this.state.plainPassword
            && this.state.email && this.state.number
        ){
            if (this.state.plainPassword === this.state.password){
                let elem = document.getElementById('register');
                let token = elem.dataset.token;
                let data = {};
                data.token = token;
                data.credentials = {};
                data.credentials.email = this.state.email;
                data.credentials.number = this.state.number;
                data.credentials.password = this.state.password;
                data.credentials.sex = this.state.isMan;
                axios.post('/api/register', data)
                    .then(res => {
                        this.setState({
                            message: 'An email was sent to ' + this.state.email + ' to confirm your profile',
                            type: 'success',
                            number: null,
                            email: null,
                            password: null,
                            plainPassword: null
                        });
                    });
            }
        }
    }

    handleCheckbox(){
        this.setState({
            isMan: !this.state.isMan
        })
    }
    render() {
        const {isLoaded, data, type, message, reset, isMan} = this.state;
        if (!isLoaded)
        {
            return (
                <div></div>
            )
        }
        else {
            return (
                <div>
                    <Logger message={message} type={type}/>
                    <div className="register-wrap">
                        <form onChange={this.handleForm} onSubmit={this.handleSubmit} method="post">
                            <div className="custom-control custom-switch flex-row align-items-center justify-content-around flex w-25">
                                <div className="m-4">I'm a woman</div>
                                <input type="checkbox" className="custom-control-input m-auto" id="isMan" name="isMan" onChange={this.handleCheckbox} defaultChecked={isMan}/>
                                <label className="custom-control-label m-auto" htmlFor="isMan">I'm a man</label>
                            </div>
                            <div className="form-group">
                                <label htmlFor="email">Email</label>
                                <input type="email" name="email" className="form-control" required/>
                            </div>
                            <div className="form-group">
                                <label htmlFor="number">Phone number</label>
                                <div className="input-group-prepend">
                                    <div className="input-group-text"> +41</div>
                                    <input type="text" name="number" className="form-control" required/>
                                </div>
                            </div>
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
                </div>
            )
        }
    }
}
ReactDOM.render(<Registration />, document.getElementById('register'));
