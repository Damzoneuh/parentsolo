import React, {Component} from 'react';
import axios from 'axios';
import {library} from '@fortawesome/fontawesome-svg-core';
import {faBars, faComments, faUser, faSpa} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
library.add(faBars, faComments, faUser, faSpa);


export default class UnderNav extends Component{
    constructor(props) {
        super(props);

    }


    render() {
        return (
            <div className="bg-dark flex flex-row justify-content-between align-items-center">
                <div>
                    <ul className="navbar-nav">
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
                </div>
                <div>
                    <ul className="navbar-nav flex flex-row justify-content-center align-items-center">
                        <li className="nav-item border-under-nav"><a href={"#"} className="badged-icons border-ul-under"><FontAwesomeIcon icon={"comments"} color={"rgb(255, 255, 255)"} /></a></li>
                        <li className="nav-item border-under-nav"><a href={"#"} className="badged-icons border-ul-under"><FontAwesomeIcon icon={"user"} color={"rgb(255, 255, 255)"} /></a></li>
                        <li className="nav-item"><a href={"#"} className="badged-icons border-ul-under"><FontAwesomeIcon icon={"spa"} color={"rgb(255, 255, 255)"} /></a></li>
                    </ul>
                </div>
            </div>
        );
    }


}