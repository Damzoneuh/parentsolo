import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
let el = document.querySelector('#viewer');

export default class ImageViewer extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
        }
    }


    render() {
        return (
            <img src={'https://parentsolo.backndev.fr/api/img/render/' + el.dataset.path} alt={el.dataset.alt} className={el.dataset.class}/>
        );
    }
}
ReactDOM.render(<ImageViewer/>, document.getElementById('viewer'));
