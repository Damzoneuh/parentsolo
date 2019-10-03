import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from '@fortawesome/fontawesome-svg-core';
import { faLockOpen, faLock } from "@fortawesome/free-solid-svg-icons";
import logo from '../../fixed/logo_noir.png';


export default class Nav extends Component{
    constructor(props){
        super(props);
        library.add(faLockOpen, faLock);
        this.state = {
            lang: [],
            link: [],
            connection: [],
            flags: [],
            isLoaded: false,
            scroll: 0
        };
        this.handleLang = this.handleLang.bind(this);
        this.scrollHandler = this.scrollHandler.bind(this);
    }

    componentDidMount(){
        axios.get('/api/nav')
            .then(res => {
                this.setState({
                    link : res.data.links,
                    connection : res.data.connection,
                    lang: res.data.lang,
                    isLoaded: true
                })
            });
        window.addEventListener('scroll', this.scrollHandler, true);
    }

    componentWillUnmount() {
        window.removeEventListener('scroll', this.scrollHandler);
    }

    handleLang(e){
        let data = {
            lang: e.target.value
        };
        axios.post("/api/lang", data)
            .then(res => {
                if (res.data === 'ok'){
                    document.location.href = document.location.pathname
                }
            })
    }

    scrollHandler(){
        if (window.scrollY === 0){
            this.setState({
                scroll: 0
            })
        }
        else {
            this.setState({
                scroll: 1
            })
        }
    }

    render() {
        const {isLoaded, lang, link, connection, scroll} = this.state;
        if (isLoaded){
            return (
                <nav className={scroll === 0 ? "navbar navbar-expand-lg navbar-light z" : "navbar navbar-expand-lg navbar-light bg-light z fixed-top"} >
                    <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText"
                            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span className="navbar-toggler-icon"></span>
                    </button>
                    <div className="collapse navbar-collapse justify-content-between align-items-center" id="navbarText">
                        <ul className="navbar-nav ">
                            <li className="nav-item"><a className="nav-link" href={link.home.path}>{link.home.name}</a></li>
                            <li className="nav-item"><a className="nav-link" href={link.testimony.path}>{link.testimony.name}</a></li>
                            <li className="nav-item"><a className="nav-link" href={link.faq.path}>{link.faq.name}</a></li>
                        </ul>
                        <img src={logo} alt="logo" className={scroll === 0 ? "none" : "nav-logo"}/>
                        <form className="form-inline">
                            <a className="nav-link custom-link" href={connection.path}>{connection.name}</a><FontAwesomeIcon icon="lock-open" color={"rgba(0, 0, 0, 0.5)"} className={"pad-right-10"}/>
                            <select onChange={this.handleLang} defaultValue={lang.selected} className="form-control">
                                <option value={lang.de.name} >{lang.de.name}</option>
                                <option value={lang.en.name} >{lang.en.name}</option>
                                <option value={lang.fr.name} >{lang.fr.name}</option>
                            </select>
                        </form>
                    </div>
                </nav>
            )
        }
        return (
            <div>
                navbar
            </div>
        );
    }

}
ReactDOM.render(<Nav />, document.getElementById('nav'));