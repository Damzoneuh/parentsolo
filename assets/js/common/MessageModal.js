import React, {Component} from 'react';
import LogoForLang from "./LogoForLang";

export default class MessageModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
            text: null,
            message: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleSend = this.handleSend.bind(this);
    }

    handleChange(e){
        this.setState({
            text: e.target.value
        })
    }

    handleSend(e){
        e.preventDefault();
        this.props.handleSend({action: 'send', message: this.state.text});
    }

    handleClose(){
        this.props.handleClose();
    }


    render() {
        const {validate} = this.props;
        return (
            <div className="custom-modal">
                <div className="d-flex flex-row justify-content-end">
                    <button type="button" className="close" onClick={this.handleClose}>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div className="text-center">
                    <LogoForLang color={'black'} baseline={true} />
                </div>
                <form className="text-center custom-modal-content" onSubmit={this.handleSend}>
                    <textarea className="form-control" name="text" onChange={this.handleChange}/>
                    <div className="d-flex flex-row justify-content-around align-items-center">
                        <button className="btn btn-primary btn-group">{validate}</button>
                    </div>
                </form>
            </div>
        );
    }

}