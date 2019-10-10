import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import UnderNav from "../../common/UnderNav";
export default class Dashboard extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        return (
            <div>
                <UnderNav/>
                dashboard
            </div>
        );
    }
}

ReactDOM.render(<Dashboard/>, document.getElementById('dashboard'));