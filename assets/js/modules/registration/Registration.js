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
            message: null
        };
        this.handleForm = this.handleForm.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleForm(e){
        this.setState({
            [e.target.name] : e.target.value
        })
    }

    handleSubmit(e){
        e.preventDefault();
        if (
            this.state.password && this.state.plainPassword
            && this.state.email && this.state.number
        ){
            console.log('ici');
            if (this.state.plainPassword === this.state.password){
                console.log('la');
                let elem = document.getElementById('register');
                let token = elem.dataset.token;
                let data = {};
                data.token = token;
                data.credentials = {};
                data.credentials.email = this.state.email;
                data.credentials.number = this.state.number;
                data.credentials.password = this.state.password;
                axios.post('/api/register', data)
                    .then(res => {
                        this.setState({
                            message: 'An email was sent to ' + this.state.email + ' to confirm your profile',
                            type: 'success'
                        });
                        this.clearForm();
                    });
            }
        }
    }

    clearForm(){
        this.setState({
            number: null,
            email: null,
            password: null,
            plainPassword: null
        })
    }

    render() {
        const {isLoaded, data, type, message} = this.state;
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
                        <form onChange={this.handleForm}>
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
                        </form>
                        <div className="marg-top-10">
                            <button className="btn btn-group-lg btn-primary" onClick={this.handleSubmit}>Register</button>
                        </div>
                    </div>
                </div>
            )
        }
    }
}
ReactDOM.render(<Registration />, document.getElementById('register'));
