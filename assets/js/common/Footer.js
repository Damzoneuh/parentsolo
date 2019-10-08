import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import logo from '../../fixed/Logo_ParentsoloFR_Blanc.png';
import logoPostFinance from '../../fixed/Logo_PostFinance.png';
import fb from '../../fixed/Facebook.png';
import twit from '../../fixed/Twitter.png';

export default class Footer extends Component{
    constructor(props){
        super(props);
        this.state= {
            isLoaded: false,
            links: [],
        }
    }
    componentDidMount(){
        axios.get('/api/footer')
            .then(res => {
               this.setState({
                   links: res.data,
                   isLoaded: true
               })
            })
    }

    handleSub(){
        window.location.hash='#'
    }


    render() {
        const {isLoaded, links} = this.state;
        if (isLoaded){
            return (
                <div className="footer-wrap">
                    <div className="row ext-row">
                        <div className="col-lg-6 col-md-6 col-sm-12">
                            <div className="flex flex-column justify-content-center align-items-center h-100 w-75 m-auto">
                                <div>
                                    <button className="btn btn-lg btn-success " onClick={() => this.handleSub()}>{links.sub}</button>
                                </div>
                                <div className="bigger">
                                    <a href="/">{links.home}</a>|
                                    <a href="#">{links.diary}</a>|
                                    <a href="#">{links.faq}</a>|
                                    <a href="#">{links.testimony}</a>|
                                    <a href="#">{links.contact}</a>
                                </div>
                                <div className="smaller ">
                                    <a href="#">{links.cgu}</a>|
                                    <a href="#">{links.press}</a>|
                                    <a href="#">{links.add}</a>
                                    <div>© Parentsolo.ch - 2009 / 2019</div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-6 col-md-6 col-sm-12">
                            <div className="row inner-row">
                                <div className="col-xl-6 col-lg-12">
                                    <div className="flex flex-column justify-content-center align-items-center h-100">

                                        <div className="top-back text-center">
                                            {links.payment}
                                        </div>
                                        <img className="footer-logo-post" src={logoPostFinance} alt="logo"/>

                                    </div>
                                </div>
                                <div className="col-xl-6 col-lg-12">
                                    <div className="flex flex-column justify-content-center align-items-center h-100">
                                        <div className="text-center">
                                            <img src={logo} alt={"logo"} className="footer-logo"/>
                                        </div>
                                        <div className="flex flex-row justify-content-between align-items-center">
                                            <div className="follow">
                                                {links.follow}
                                            </div>
                                            <a href="#"><img src={fb} alt="facebook" className="follow-logo"/></a>
                                            <a href="#"><img src={twit} alt="twitter" className="follow-logo"/></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }
        else {return (<div></div>)}
    }
}
ReactDOM.render(<Footer/>, document.getElementById('footer'));