import React, {Component} from 'react';
import axios from 'axios';
import HBL from '../../../fixed/Coeur-Bas_R.svg';
import HBR from '../../../fixed/Coeur-Bas_L.svg';
import HTL from '../../../fixed/Coeur-Haut_R.svg';
import HTR from '../../../fixed/Coeur-Haut_L.svg';

//TODO update after profile

export default class AutoSearch extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            trans: []
        }
    }

    componentDidMount(){
        axios.get('/api/trans/matching')
            .then(res => {
                this.setState({
                    trans: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, trans} = this.state;
        if (isLoaded){
            return (
                <div className="col-sm-12 col-md-6 font-size-20">
                    <div className={"testimony-wrap"}>
                        <h3>MATCHING</h3>
                        <p className="text-center marg-top-20">{trans.cupidon}</p>
                        <div className="d-flex flex-row marg-top-50">
                            <div className="w-25">
                                <div>
                                    <img src={HTL} alt="heart" className="heart-top"/>
                                </div>
                                <div className="text-right">
                                    <img src={HBL} alt="heart" className="heart-bottom"/>
                                </div>
                            </div>
                            <div className="w-75 d-flex justify-content-around align-items-center">
                                <button className="btn-lg btn btn-group btn-outline-light launch-button">{trans.launch} !</button>
                            </div>
                            <div className="w-25">
                                <div className="text-right">
                                    <img src={HTR} alt="heart" className="heart-top"/>
                                </div>
                                <div>
                                    <img src={HBR} alt="heart" className="heart-bottom"/>
                                </div>
                            </div>
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