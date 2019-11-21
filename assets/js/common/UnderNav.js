import React, {Component} from 'react';
import {library} from '@fortawesome/fontawesome-svg-core';
import {faBars, faComments, faUser, faSpa} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import ImageRenderer from "./ImageRenderer";
library.add(faBars, faComments, faUser, faSpa);
let el = document.getElementById('dashboard');
import axios from 'axios';

export default class UnderNav extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            display: el.dataset.display,
            isMan: el.dataset.isMan,
            img: el.dataset.img,
            complete: el.dataset.complete,
            user: [],
            links: []
        };
        this.handleSub = this.handleSub.bind(this);
        this.handleShop = this.handleShop.bind(this);
        this.handleTestimony = this.handleTestimony.bind(this);
    }

    componentDidMount(){
        axios.get('/api/user')
            .then(res => {
                this.setState({
                    user: res.data
                });
                axios.get('/api/footer')
                    .then(res => {
                        this.setState({
                            links: res.data,
                            isLoaded: true
                        })
                    })
            })
    }

    handleSub(){
        window.location.hash='#'
    }

    handleShop(){
        window.location.hash='/shop'
    }

    handleTestimony(){
        window.location.hash='/testimony'
    }

    render() {
        //TODO pas mis les img au cas ou y en a pas
        const {display, isMan, img, complete, user, isLoaded, links} = this.state;
        if (isLoaded){
            return (
                <div className="black-80 flex flex-row justify-content-between align-items-center">
                    <div className="d-flex flex-row align-items-center justify-content-center">
                        <ul className="navbar-nav marg-10">
                            <li className="nav-item dropdown">
                                <a className="nav-link " href="#" id="underNavDropdownMenuLink"
                                   role="button" data-toggle="dropdown" aria-haspopup="true">
                                    <FontAwesomeIcon icon={'bars'} color={"rgb(255,255,255)"} className={"marg-10 ham-under"}/>
                                </a>
                                <div className="dropdown-menu" aria-labelledby="UnderNavDropdownMenuLink">
                                    <a className="dropdown-item" href="#">Link 1</a>
                                    <a className="dropdown-item" href="#">Link 2</a>
                                </div>
                            </li>
                        </ul>
                        {!user.isSub ? <button className="btn btn-lg btn-outline-success pulse under-nav-button" onClick={() => this.handleSub}>{links.sub}</button> : ''}
                        {user.isSub && !user.isPremium ? <button className="btn btn-lg btn-outline-success pulse under-nav-button" onClick={() => this.handleShop}>{links.goShop}</button> : ''}
                        {user.isSub && user.isPremium ? <button className="btn btn-lg btn-outline-success pulse under-nav-button" onClick={() => this.handleTestimony}>{links.letTestimony}</button> : ''}
                    </div>
                    <div className="w-50">
                        <ul className="navbar-nav flex flex-row justify-content-center align-items-center">
                            <li className="nav-item border-under-nav"><a href={"#"} className="badged-icons border-ul-under"><FontAwesomeIcon icon={"comments"} color={"rgb(255, 255, 255)"} /></a></li>
                            <li className="nav-item border-under-nav"><a href={"#"} className="badged-icons border-ul-under"><FontAwesomeIcon icon={"user"} color={"rgb(255, 255, 255)"} /></a></li>
                            <li className="nav-item"><a href={"#"} className="badged-icons border-ul-under"><FontAwesomeIcon icon={"spa"} color={"rgb(255, 255, 255)"} /></a></li>
                        </ul>
                    </div>
                    <div>
                        {img ? <ImageRenderer id={img} alt={"profil-img"} className={"testimony-img marg-10"} /> :
                            isMan ? '' : '' }
                    </div>
                </div>
            );
        }
        else{
            return (<div></div>)
        }
    }
}