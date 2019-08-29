import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Image from "../../common/Image";

export default class Profil extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: true, //TODO gérer le loader
            tab: 0
        }
    }


    render() {
        const {isLoaded, tab} = this.state;
        if (!isLoaded){
            return(
                <div className="container-loader">
                    <div className="ring">
                        <span className="ring-span"></span>
                    </div>
                </div>
            )
        }
        if (tab === 0){
            return (
                <Image/>
            )
        }
    }

}

ReactDOM.render(<Profil/>, document.getElementById('profil'));