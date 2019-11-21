import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from '@fortawesome/fontawesome-svg-core';
import { faCheck, faTimes } from "@fortawesome/free-solid-svg-icons";
library.add(faCheck, faTimes);

export default class Shop extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: false,
            trans: []
        }
    }

    componentDidMount(){
        axios.get('/api/shop')
            .then(res => {
                this.setState({
                    isLoaded: true,
                    trans: res.data
                });

            })
    }

    render() {
        const {isLoaded, trans} = this.state;
        if (isLoaded){
            return (
                <div className="container-fluid">
                    <div className="col-12 col-md-10 offset-md-1">
                        <div className="table-responsive">
                            <table className="table table-bordered">
                                <thead className="bg-danger text-white">
                                    <tr>
                                        <td scope="col">{trans.function}</td>
                                        <td scope="col" className="text-center">{trans.registered}</td>
                                        <td scope="col" className="text-center">{trans.basic}</td>
                                        <td scope="col" className="text-center">{trans.medium}</td>
                                        <td scope="col" className="text-center">{trans.premium}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.profilCreate}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.profilConsult}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.profilSearch}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.messageReceive}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.flowerReceive}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.messageSend}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.groupJoin}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.flowerSend}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center">{trans.options} *</td>
                                        <td scope="col" className="text-center">5/{trans.month} <br/>+ {trans.options} *</td>
                                        <td scope="col" className="text-center text-success">{trans.unlimited}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.favoriteList}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center">{trans.options} *</td>
                                        <td scope="col" className="text-center">5/{trans.month} <br/>+ {trans.options} *</td>
                                        <td scope="col" className="text-center text-success">{trans.unlimited}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.groupCreate}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">Matching</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            );
        }
        else {
            return (<div> </div>)
        }
    }
}

ReactDOM.render(<Shop/>, document.getElementById('shop'));
