import React, {Component} from 'react';
import axios from 'axios';

export default class TalkingThreatSubscribe extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: false,
            threat: [],
            relation: null,
            sex: null,
            tab: 1,
            animationText: null,
            day: null,
            month: null,
            year: null,
            major: true,
            canton: [],
            email: null,
            cities: [],
            selectedCanton: null,
            selectedCity: null,
            phone: null,
            password: null,
            plainPassword: null,
            passwordError: false,
            phoneScreen: this.props.phone
        };
        this.handleAnimation = this.handleAnimation.bind(this);
        this.handleTab = this.handleTab.bind(this);
        this.handleDate = this.handleDate.bind(this);
        this.handleDateTab = this.handleDateTab.bind(this);
        this.handleCanton = this.handleCanton.bind(this);
        this.handleCity = this.handleCity.bind(this);
        this.handlePhone = this.handlePhone.bind(this);
        this.nextTab = this.nextTab.bind(this);
        this.handlePassword = this.handlePassword.bind(this);
    }

    componentDidMount(){
        axios.get('/api/talking/subscribe')
            .then(res => {
                this.setState({
                    threat: res.data,
                    isLoaded: true
                });
                this.handleAnimation();
            });
        axios.get('/api/canton')
            .then(res => {
                this.setState({
                    canton: res.data
                })
            })
    }

    nextTab(e){
        e.preventDefault();
       if (this.state.tab < 9){
           this.setState({
               tab: this.state.tab +1
           })
       }
    }

    handleAnimation(){
        let text = this.state.threat.first[0];
        let length = text.length;
        let i = 0;
        setInterval(() => {
            if (i < length){
                i++;
                this.setState({
                    animationText: text.substring(0, i)
                })
            }
            else {
                setTimeout(() => {i = -1}, 5000);
            }
        }, 100)
    }

    handleTab(e){
        if (this.state.tab < 9){
            this.setState({
                tab : this.state.tab +1,
                [e.target.name]: e.target.value
            });
        }
    }

    handleDate(e){
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleDateTab(e){
        e.preventDefault();
        let today = new Date();
        if (today.getFullYear() -18 > parseInt(this.state.year)){
            this.setState({
                tab: this.state.tab +1
            })
        }
        if (today.getFullYear() -18 === parseInt(this.state.year)){
            if (today.getMonth() === parseInt(this.state.month)){
                if (today.getDay() > parseInt(this.state.day)){
                    this.setState({
                        major: false
                    })
                }
                else {
                    this.setState({
                        tab: this.state.tab +1
                    })
                }
            }
            if (today.getMonth() > parseInt(this.state.month)){
                this.setState({
                    major: false
                })
            }
            else {
                this.setState({
                    tab: this.state.tab +1
                })
            }
        }
        else {
            this.setState({
                major: false
            })
        }
    }

    handleCanton(e){
        let value = e.target.value;
        axios.get('/api/cities/' + value)
            .then(res => {
                this.setState({
                    selectedCanton: value,
                    cities: res.data
                })
            })
    }

    handleCity(e){
        this.setState({
            selectedCity: e.target.value
        })
    }

    handlePhone(e){
        if (this.state.tab === 7 ){
            this.setState({
                passwordError: false
            })
        }
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handlePassword(e){
        e.preventDefault();
        if (this.state.password === this.state.plainPassword && this.state.plainPassword){
            let data = {
                token: document.getElementById('register').dataset.token,
                credentials: {
                    email: this.state.email,
                    sex: this.state.sex,
                    city: this.state.selectedCity,
                    number: this.state.phone,
                    password: this.state.password,
                    relation: this.state.relation,
                    day: this.state.day,
                    month: this.state.month,
                    year: this.state.year
                }
            };
            axios.post('/api/register', data)
                .then(res => {
                    this.setState({
                        tab: this.state.tab +1
                    })
                })
        }
        else {
            this.setState({
                passwordError: true
            })
        }
    }

    render() {
        const {threat, isLoaded, tab, animationText, major, canton, cities, selectedCanton, selectedCity, passwordError} = this.state;
        const {isPhone} = this.props;
        if (isLoaded && tab === 1){
            return(
                <div className="flex flex-column align-items-center justify-content-center">
                    <div className="marg-top-10 threat">
                        <div className="d-flex flex-row justify-content-center">
                            <div className="flex-row d-flex sophie-wrap">
                                <div className="sophie"></div>
                            </div>
                        </div>
                        <div className="triangle"></div>
                        <div className="img-bubble d-flex flex-column align-items-center justify-content-center text-center">
                            {animationText} <br/><span className="threat-red pulse">{threat.first[1]}</span>
                        </div>
                    </div>
                    <div className="d-flex flex-row align-items-center justify-content-end">
                        <div className={isPhone ? "w-50 marg-top-10 text-center" : "d-flex flex-row align-items-center justify-content-center w-100 marg-top-10"}>
                            <button className="btn btn-group btn-danger marg-10" name="relation" value={threat.firstButton.lovely.value} onClick={this.handleTab}>
                                {threat.firstButton.lovely.text}</button>
                            <button className="btn btn-group btn-danger marg-10" name="relation" value={threat.firstButton.friendly.value} onClick={this.handleTab}>
                                {threat.firstButton.friendly.text}</button>
                            <button className="btn btn-group btn-danger marg-10" name="relation" value={threat.firstButton.both.value} onClick={this.handleTab}>
                                {threat.firstButton.both.text}</button>
                        </div>
                    </div>
                </div>

            )
        }
        if (isLoaded && tab === 2){
            return (
                <div className="flex flex-column align-items-center justify-content-center">
                    <div className="marg-top-10 threat">
                        <div className="flex-row flex sophie-wrap">
                            <div className="sophie"></div>
                        </div>
                        <div className="triangle"></div>
                        <div className="img-bubble flex flex-column align-items-center justify-content-center text-center">
                            {threat.second[0]} <br/><span className="threat-red size-more">{threat.second[1]}</span>
                        </div>
                    </div>
                    <div className="flex flex-row align-items-center justify-content-center w-100 marg-top-10">
                        <button className="btn btn-group btn-danger marg-10" name="sex" value={threat.secondButton.daddy.value} onClick={this.handleTab}>
                            {threat.secondButton.daddy.text}</button>
                        <button className="btn btn-group btn-danger marg-10" name="sex" value={threat.secondButton.mom.value} onClick={this.handleTab}>
                            {threat.secondButton.mom.text}</button>
                    </div>
                </div>
            )
        }
        if (isLoaded && tab === 3){
            return (
                <div className="flex flex-column align-items-center justify-content-center">
                    <div className="marg-top-10 threat">
                        <div className="flex-row flex sophie-wrap">
                            <div className="sophie"></div>
                        </div>
                        <div className="triangle"></div>
                        <div className="img-bubble flex flex-column align-items-center justify-content-center text-center">
                            {major ? <p>{threat.third[0]} <br/><span className="threat-red size-more"> {threat.third[1]}</span></p> :
                                <span className="threat-red size-more">{threat.thirdError.text}</span>}
                        </div>
                    </div>

                    <div className="flex flex-row align-items-center justify-content-center w-100 marg-top-10">
                        <form className="w-50">
                            <div className="form-row align-items-center">
                              <div className="col">
                                  <input className="day" type="text" maxLength="2" name="day" onChange={this.handleDate}
                                         placeholder={threat.third[1].substring(threat.third[1].length -10, threat.third[1].length -8)}/>
                              </div>
                              <div className="col">
                                  <input className="month" type="text" maxLength="2" name="month" onChange={this.handleDate}
                                         placeholder={threat.third[1].substring(threat.third[1].length -7, threat.third[1].length -5)}/>
                              </div>
                              <div className="col">
                                  <input className="year" type="text" maxLength="4" name="year" onChange={this.handleDate}
                                         placeholder={threat.third[1].substring(threat.third[1].length -4, threat.third[1].length)}/>
                              </div>
                                <div className="col">
                                    <button className="btn btn-group btn-danger marg-10" onClick={this.handleDateTab}>
                                        {threat.thirdButton.text}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            )
        }
        if (isLoaded && tab === 4){
            return (
                <div>
                    <div className="flex flex-column align-items-center justify-content-center">
                        <div className="marg-top-10 threat">
                            <div className="flex-row flex sophie-wrap">
                                <div className="sophie"></div>
                            </div>
                            <div className="triangle"></div>
                            <div className="img-bubble flex flex-column align-items-center justify-content-center text-center">
                                {!selectedCity ? <p>{threat.fourth.text[0]} <br/><span className="threat-red size-more">{threat.fourth.text[1]}</span></p> :
                                    <h2>{threat.fourth.response[0]}</h2>
                                }
                            </div>
                        </div>
                        <div className="flex flex-row align-items-center justify-content-center w-100 marg-top-10">
                            <form>
                                <div className="form-group">
                                    <select defaultChecked={null} onChange={this.handleCanton}>
                                        <option value={null}>{threat.fourth.labels.canton}</option>
                                        {canton.map(c => {
                                            return(
                                                <option value={c.id} key={c.id}>{c.name}</option>
                                            )
                                        })}
                                    </select>
                                </div>
                                {cities && selectedCanton ?
                                    <div className="form-group">
                                        <select defaultChecked={null} onChange={this.handleCity}>
                                            <option value={null}>{threat.fourth.labels.city}</option>
                                            {cities.map(ci => {
                                                return(
                                                    <option value={ci.id} key={ci.id}>{ci.name}</option>
                                                )
                                            })}
                                         </select>
                                    </div> : ''}
                            </form>
                            {selectedCanton ? <button className="btn btn-group btn-danger marg-10" onClick={this.nextTab}>{threat.seventhButton.text}</button> : ''}
                        </div>
                    </div>
                </div>
            )
        }
        if (isLoaded && tab === 5) {
            return (
                <div className="flex flex-column align-items-center justify-content-center">
                    <div className="marg-top-10 threat">
                        <div className="flex-row flex sophie-wrap">
                            <div className="sophie"></div>
                        </div>
                        <div className="triangle"></div>
                        <div className="img-bubble flex flex-column align-items-center justify-content-center text-center">
                            {threat.fifth[0]} <br/><span className="threat-red size-more">{threat.fifth[1]}</span>
                        </div>
                    </div>
                    <div className="flex flex-row align-items-center justify-content-center w-100 marg-top-10">
                        <form>
                            <div className="col-auto">
                                <div className="input-group">
                                    <div className="input-group-prepend">
                                        <div className="input-group-text">+41</div>
                                    </div>
                                    <input type="text" name="phone" className="form-control" onChange={this.handlePhone}/>
                                </div>
                            </div>
                        </form>
                        <button className="btn btn-group btn-danger marg-10" onClick={this.nextTab}>
                            {threat.sixthButton.text}</button>
                    </div>
                </div>
            )
        }
        if (isLoaded && tab === 6){
            return(
                <div className="flex flex-column align-items-center justify-content-center">
                    <div className="marg-top-10 threat">
                        <div className="flex-row flex sophie-wrap">
                            <div className="sophie"></div>
                        </div>
                        <div className="triangle"></div>
                        <div className="img-bubble flex flex-column align-items-center justify-content-center text-center">
                            {threat.sixth[0]} <br/><span className="threat-red size-more">{threat.sixth[1]}</span>
                        </div>
                    </div>
                    <form>
                        <div className="form-row align-items-center">
                            <div className="col">
                                <input type="email" name="email" onChange={this.handlePhone} placeholder={"Email"}/>
                            </div>
                            <div className="col">
                                <button className="btn btn-group btn-danger marg-10 " onClick={this.nextTab}>
                                    {threat.sixthButton.text}</button>
                            </div>
                        </div>
                    </form>
                </div>
            )
        }
        if (isLoaded && tab === 7){
            return (
                <div className="flex flex-column align-items-center justify-content-center">
                    <div className="marg-top-10 threat">
                        <div className="flex-row flex sophie-wrap">
                            <div className="sophie"></div>
                        </div>
                        <div className="triangle"></div>
                        <div className="img-bubble flex flex-column align-items-center justify-content-center text-center">
                            {threat.seventh[0]}
                        </div>
                    </div>
                    <form className="w-75" onChange={this.handlePhone}>
                        <div className="form-group marg-top-10">
                            <input type="password" name="password" className={!passwordError ? "form-control" : "form-control is-invalid"} id="password" placeholder={threat.seventh[1]}
                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{6,22}$"/>
                            <div className="invalid-feedback">
                                {threat.seventhError.text}
                            </div>
                        </div>
                        <div className="form-group">
                            <input type="password" name="plainPassword" className="form-control" id="plainPassword" placeholder={threat.seventh[2]}/>
                        </div>
                        <button className="btn btn-group btn-danger marg-10" onClick={this.handlePassword}>
                            {threat.sixthButton.text}</button>
                    </form>
                </div>
            )
        }
        if (isLoaded && tab === 8){
           return (
               <div className="flex flex-column align-items-center justify-content-center">
                   <div className="marg-top-10 threat">
                       <div className="flex-row flex sophie-wrap">
                           <div className="sophie"></div>
                       </div>
                       <div className="triangle"></div>
                       <div className="img-bubble flex flex-column align-items-center justify-content-center text-center">
                           {threat.final[0]}
                       </div>
                   </div>
               </div>
           )
        }
        else {
            return (
                <div>

                </div>
            );
        }
    }

}