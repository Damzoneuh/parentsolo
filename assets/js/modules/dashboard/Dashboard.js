import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import UnderNav from "../../common/UnderNav";
import LightSearch from "./LightSearch";
import AutoSearch from "./AutoSearch";
import LastProfiles from "./LastProfiles";
import Groups from "./Groups";
import Diary from "./Diary";
import Adsense from "../../common/Adsense";
import Testimony from "./Testimony";
import News from "./News";

//TODO back the props after selected profile

export default class Dashboard extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        return (
            <div>
                <UnderNav/>
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-md-9 col-sm-12">
                            <div className="row">
                                <LightSearch/>
                                <AutoSearch/>
                            </div>
                            <LastProfiles/>
                            <div className="row">
                                <div className="col-sm-12 col-md-6">
                                    <Groups/>
                                </div>
                                <div className="col-sm-12 col-md-6">
                                    <Diary/>
                                </div>
                            </div>
                        </div>
                        <div className="col-sm-12 col-md-3">
                            <Adsense/>
                            <Testimony />
                            <News/>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

ReactDOM.render(<Dashboard/>, document.getElementById('dashboard'));