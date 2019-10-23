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
import ShowAllProfile from "../../common/ShowAllProfile";

//TODO redirect on profile search where this page will done


export default class Dashboard extends Component{
    constructor(props) {
        super(props);
        this.state = {
            tab: 1,
            profile: null,
            search: [],
            trans: []
        };
        this.handleTab = this.handleTab.bind(this);
        this.handleProfile = this.handleProfile.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
    }

    componentDidMount(){
        axios.get('/api/trans/search')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })
    }


    handleTab(value){
        this.setState({
            tab: value
        })
    }

    handleProfile(value){
        this.setState({
            profile: value
        })
    }

    handleSearch(value){
        this.setState({
            search: value
        });
    }

    render() {
        const {search, tab, trans} = this.state;
        if (tab === 1){
            return (
                <div>
                    <UnderNav/>
                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-lg-9 col-sm-12">
                                <div className="row">
                                    <LightSearch handleSearch={this.handleSearch} handleTab={this.handleTab}/>
                                    <AutoSearch handleSearch={this.handleSearch} handleTab={this.handleTab}/>
                                </div>
                                <LastProfiles handleProfil={this.handleProfile}/>
                                <div className="row">
                                    <div className="col-sm-12 col-lg-6">
                                        <Groups/>
                                    </div>
                                    <div className="col-sm-12 col-lg-6">
                                        <Diary/>
                                    </div>
                                </div>
                            </div>
                            <div className="col-sm-12 col-lg-3">
                                <Adsense/>
                                <Testimony />
                                <News/>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }
        if (tab === 2){
            return (
                <div className="bg-black-10">
                    <UnderNav/>
                    <div className="banner-search d-flex flex-row justify-content-around align-items-center">
                        <button className="btn btn-group-lg btn-lg btn-outline-danger marg-top-100 marg-bottom-100">{trans.newSearch}</button>
                    </div>
                    <ShowAllProfile handleTab={this.handleTab} handleSearch={this.handleSearch} search={search} trans={trans}/>
                </div>
            )
        }
    }
}

ReactDOM.render(<Dashboard/>, document.getElementById('dashboard'));