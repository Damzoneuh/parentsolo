import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Logger from "../../common/Logger";
import bannerOne from '../../../fixed/iStock-1034831832.png';
import bannerTwo from '../../../fixed/iStock-514318934.png';
import bannerThree from '../../../fixed/banner3.png';

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
            isMan: false,
            activeImg: 1
        };
        this.handleForm = this.handleForm.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleCheckbox = this.handleCheckbox.bind(this);
        this.CarouselHandler = this.CarouselHandler.bind(this);
    }

    componentDidMount(){
        setInterval(() => this.CarouselHandler(), 6000)
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

    CarouselHandler(){
        let active = this.state.activeImg;
        if (active === 3){
            active = 1
        }
        else {
            active = active + 1
        }
        this.setState({
            activeImg: active
        })
    }

    render() {
        const {isLoaded, data, type, message, reset, isMan, activeImg} = this.state;
        if (!isLoaded)
        {
            return (
                <div></div>
            )
        }
        else {
            return (
                    <div className="register-wrap">
                        <img src={bannerOne} className={activeImg === 1 ? "w-100 banner" : "w-100 none"}/>
                        <img src={bannerTwo} className={activeImg === 2 ? "w-100 banner" : "w-100 none"}/>
                        <img src={bannerThree} className={activeImg === 3 ? "w-100 banner" : "w-100 none"}/>
                        {/*<form onChange={this.handleForm} onSubmit={this.handleSubmit} method="post">*/}
                        {/*    <div className="custom-control custom-switch flex-row align-items-center justify-content-around flex w-25">*/}
                        {/*        <div className="m-4">I'm a woman</div>*/}
                        {/*        <input type="checkbox" className="custom-control-input m-auto" id="isMan" name="isMan" onChange={this.handleCheckbox} defaultChecked={isMan}/>*/}
                        {/*        <label className="custom-control-label m-auto" htmlFor="isMan">I'm a man</label>*/}
                        {/*    </div>*/}
                        {/*    <div className="form-group">*/}
                        {/*        <label htmlFor="email">Email</label>*/}
                        {/*        <input type="email" name="email" className="form-control" required/>*/}
                        {/*    </div>*/}
                        {/*    <div className="form-group">*/}
                        {/*        <label htmlFor="number">Phone number</label>*/}
                        {/*        <div className="input-group-prepend">*/}
                        {/*            <div className="input-group-text"> +41</div>*/}
                        {/*            <input type="text" name="number" className="form-control" required/>*/}
                        {/*        </div>*/}
                        {/*    </div>*/}
                        {/*    <div className="form-group">*/}
                        {/*        <label htmlFor="password">Type a password</label>*/}
                        {/*        <input type="password" name="password" className="form-control" required/>*/}
                        {/*    </div>*/}
                        {/*    <div className="form-group">*/}
                        {/*        <label htmlFor="plainPassword">Confirm your password</label>*/}
                        {/*        <input type="password" name="plainPassword" className="form-control" required/>*/}
                        {/*    </div>*/}
                        {/*    <div className="marg-top-10">*/}
                        {/*        <button className="btn btn-group-lg btn-primary">Register</button>*/}
                        {/*    </div>*/}
                        {/*</form>*/}
                    </div>
            )
        }
    }
}
ReactDOM.render(<Registration />, document.getElementById('register'));
