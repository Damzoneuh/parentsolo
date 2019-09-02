import React, {Component} from 'react';
import axios from 'axios';

export default class KnowCards extends Component{
    constructor(props){
        super(props);
        this.state= {
            isLoaded: true,
            item: this.props.item
        };
        this.handlePayment = this.handlePayment.bind(this);
    }

    handlePayment(alias){
       let data = {
           token: this.props.token,
           alias: alias,
           settings: {
               amount: this.props.settings.amount,
               context: this.props.settings.context
           },
           id:this.state.item.id
       };
       console.log(data);
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
                    let data = {
                        id: this.state.item.id,
                        token: this.props.token
                    };
                    if (this.state.item.isSubscribe){
                        axios.post('/api/subscribe', data)
                            .then(res => {
                                this.props.logger({message : 'Payment succeed, You will be logout to activate your subscription', type: 'success'});
                                setTimeout(() => window.location.href = '/logout')
                            });
                    }
                    else {
                        this.props.logger({message : 'Payment succeed', type: 'success'});
                        setTimeout(() => window.location.href = '/profil')
                    }
                }
                else {
                    this.props.logger({message: 'An error is occurred during the payment', type: 'error'})
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