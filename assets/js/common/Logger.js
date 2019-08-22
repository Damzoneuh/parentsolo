import React, {Component} from 'react';

export default class Logger extends Component{
    constructor(props){
        super(props);
        this.state = {
            display: true
        };
    }

    componentDidMount(){
        setTimeout(() => {
            this.setState({
                display: false
            });
        }, 35000)
    }

    render() {
        const {display} = this.state;
        if (display && this.props.message){
            return (
                <div className={"logger-box " + (this.props.type)}>
                    {this.props.message.toUpperCase()}
                </div>
            );
        }
        else {
            return (<div className="none"></div>)
        }
    }
}