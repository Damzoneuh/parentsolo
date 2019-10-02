import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

export default class Nav extends Component{
    constructor(props){
        super(props);
        this.state = {
            lang: [],
            link: [],
            flags: [],
        }
    }
}