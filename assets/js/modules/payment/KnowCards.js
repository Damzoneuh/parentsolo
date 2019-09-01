import React, {Component} from 'react';
import axios from 'axios';

export default class KnowCards extends Component{
    constructor(props){
        super(props);
        this.state= {
            isLoaded: true
        };
        this.handlePayment = this.handlePayment.bind(this);
    }

    handlePayment(alias){
       let data = {
           token: null,
           alias: null,
           settings: {}
       };
       data.token = this.props.token;
       data.alias = alias;
       data.settings.amount = this.props.settings.amount;
       data.settings.context = this.props.settings.context;
       this.setState({
           isLoaded: false
       });
        axios.post('/api/payment/knowcard', data)
            .then(res => {
                this.setState({
                    isLoaded: true
                });
                let data = JSON.parse(res.data);
                if (data.Transaction.Status === 'AUTHORIZED'){
                    console.log(data)
                    this.props.logger({message : 'Payment succeed', type: 'success'});
                    let data = {
                        id: 5,
                        token: this.props.token
                    };
                    axios.post('/api/subscribe', data)
                        .then(res => {
                            console.log(res.data)
                        })
                }

            })
            .catch(e => {
                this.props.logger({message: 'An error is occurred during the payment', type: 'error'})
            })
    }

    render() {
        const {isLoaded} = this.state;
        const {cards} = this.props;
        if (cards.length > 0 && isLoaded) {
            return (
                <div className="marg-top-10">
                    <div className="row w-50 m-auto">
                        <div className="col-12">
                            {cards.map(card => {
                                return(
                                    <div key={card.id} className="flex-row card marg-top-10 pad-10" onClick={() => this.handlePayment(card.alias)}>
                                        <div className="col-6 text-center">
                                            {card.cardName}
                                        </div>
                                        <div className="col-6 text-center">
                                            {card.displayText}
                                        </div>
                                    </div>
                                )
                            })}
                        </div>
                    </div>
                </div>
            );
        }
        if (!isLoaded){
            return (
                <div className="container-loader">
                    <div className="ring">
                        <span className="ring-span"></span>
                    </div>
                </div>
            )
        }
    }

}