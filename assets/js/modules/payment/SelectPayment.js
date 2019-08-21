import React, {Component} from 'react';

export default class SelectPayment extends Component{
    constructor(props){
        super(props);
    }

    render() {
        return (
            <div className="row">
                <div className="col-lg-4 col-md-4 col-6">
                    <div className="card" onClick={() => this.props.handler(2)}>
                        <div className="card-body">
                            PayPal
                        </div>
                    </div>
                </div>
                <div className="col-lg-4 col-md-4 col-6" onClick={() => this.props.handler(3)}>
                    <div className="card">
                        <div className="card-body">
                            PostFinance
                        </div>
                    </div>
                </div>
                <div className="col-lg-4 col-md-4 col-6" onClick={() => this.props.handler}>
                    <div className="card">
                        <div className="card-body">
                            Credit card
                        </div>
                    </div>
                </div>
            </div>
        );
    }

}