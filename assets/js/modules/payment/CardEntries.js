import React, {Component} from 'react';
import axios from "axios";


export default class CardEntries extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: true,
            number: null,
            month: 1,
            year: null,
            cvc: null,
            holder: null,
        };
        this.handleForm = this.handleForm.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
        this.handleLogger = this.handleLogger.bind(this);
    }
    handleForm(e){
        this.setState({
            [e.target.name] : e.target.value
        })
    }
    handleFormSubmit(){
        event.preventDefault();
        this.setState({
            isLoaded: false
        });
        let data = {
            credentials: {},
            token: null,
            settings: {}
        };
        data.credentials.number = this.state.number;
        data.credentials.holder = this.state.holder;
        data.credentials.year = this.state.year;
        data.credentials.month = this.state.month;
        data.credentials.cvc = this.state.cvc;
        data.token = this.props.token;
        data.settings.amount = this.props.settings.amount;
        data.settings.context = this.props.settings.context;
        data.settings.currency = this.props.settings.currency;
        axios.post('https://parentsolo.backndev.fr/api/card', data)
            .then(res => {
                let data = JSON.parse(res.data);
                if (data.error){
                    let log = {
                        message: data.error,
                        type: "error"
                    };
                    this.props.logger(log);
                    this.setState({
                        isLoaded: true
                    });
                }
                else {
                    if (data.Transaction.Status === 'AUTHORIZED') {
                        console.log('auth');
                        let log = {
                            message: 'Payment succeeded',
                            type: 'success'
                        };
                        this.setState({
                            isLoaded: true
                        });
                        this.handleLogger(log);
                        setTimeout(() => window.location.href = '/', 5000);
                    }
                    else {
                        let log = {
                            message: 'An error was occurred during the payment',
                            type: "error"
                        };
                        this.props.logger(log);
                        this.setState({
                            isLoaded: true
                        });
                    }
                }
            })
            .catch(e => {
                let log = {
                    message: 'An error was occurred during the payment',
                    type: "error"
                };
                this.handleLogger(log);
                this.setState({
                    isLoaded: true
                });
            })
    }
    handleLogger(log){
        this.props.logger(log);
    }
    render() {
        const {isLoaded} = this.state;
        if (isLoaded) {
            return (
                <div className="container w-50 box">
                    <div className="row">
                        <form onChange={this.handleForm} className="col-12">
                            <div className="row">
                                <div className="form-group col-6 col-lg-6">
                                    <label htmlFor="number">Card number</label>
                                    <input type="text" name="number" className="form-control" id="number"
                                           pattern="([0-9]){15,16}"/>
                                </div>
                                <div className="form-group col-6 col-lg-6">
                                    <label htmlFor="holder">Holder</label>
                                    <input type="text" name="holder" className="form-control" id="holder"/>
                                </div>
                            </div>
                            <div className="form-row align-items-center justify-content-around">
                                <div className="form-group col-lg-2 col-md-6 col-12">
                                    <label htmlFor="month">Month</label>
                                    <select className="form-control" id="month" name="month">
                                        <option value={1}>1</option>
                                        <option value={2}>2</option>
                                        <option value={3}>3</option>
                                        <option value={4}>4</option>
                                        <option value={5}>5</option>
                                        <option value={6}>6</option>
                                        <option value={7}>7</option>
                                        <option value={8}>8</option>
                                        <option value={9}>9</option>
                                        <option value={10}>10</option>
                                        <option value={11}>11</option>
                                        <option value={12}>12</option>
                                    </select>
                                </div>
                                <div className="form-group col-lg-2 col-md-6 col-12">
                                    <label htmlFor="year">Year</label>
                                    <input type="text" maxLength={4} className="form-control" name="year" id="year"/>
                                </div>
                                <div className="form-group col-lg-2 col-md-6 col-12 w-25">
                                    <label htmlFor="cvc">CVC</label>
                                    <input type="text" maxLength={3} className="form-control" name="cvc" id="cvc"/>
                                </div>
                            </div>

                        </form>
                        <div className="container">
                            <div className="row marg-top-10">
                                <div className="col-lg-6 col-12 text-center">
                                    <button className="btn btn-group-lg btn-primary"
                                            onClick={this.handleFormSubmit}>Submit
                                    </button>
                                </div>
                                <div className="col-lg-6 col-12 text-center marg-top-10">
                                    <button className="btn btn-group-lg btn-primary"
                                            onClick={() => this.props.handler(1)}>Retour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            );
        }
        else {
            return (
                <div className="container-loader">
                    <div className="ring">
                        Traitement en cours
                        <span className="ring-span"></span>
                    </div>
                </div>
            )
        }
    }
}