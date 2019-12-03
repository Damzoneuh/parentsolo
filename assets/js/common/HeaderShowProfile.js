import React, {Component} from 'react';
import defaultMan from "../../fixed/HommeDefaut.png";
import defaultWoman from '../../fixed/FemmeDefaut.png';
import ImageRenderer from "./ImageRenderer";
import boyDefault from '../../fixed/GarconDefaut.png';
import girlDefault from '../../fixed/FilleDefaut.png';
import ChildImageRenderer from "./ChildImageRenderer";
import {library} from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {faComments, faSpa} from "@fortawesome/free-solid-svg-icons";
import TextAreaModal from "./TextAreaModal";
library.add(faComments, faSpa);
import axios from 'axios';
import MessageModal from "./MessageModal";
import ImgModal from "./ImgModal";
import ChildModal from "./ChildModal";

let el = document.getElementById('chat');
const es = new WebSocket('ws://ws.disons-demain.be:5000/f/' + el.dataset.user);
const messageEs = new WebSocket('ws://ws.disons-demain.be:5000/c/' + el.dataset.user);

export default class HeaderShowProfile extends Component {
    constructor(props) {
        super(props);
        this.state = {
            message: null,
            flowerText: null,
            flowerType: null,
            modalFlower: false,
            flowers: [],
            messagesModal: false
        };
        this.handleClose = this.handleClose.bind(this);
        this.handleToggleFlowerModal = this.handleToggleFlowerModal.bind(this);
        this.handleSend = this.handleSend.bind(this);
        this.handleMessagesModal = this.handleMessagesModal.bind(this);
    }

    componentDidMount(){
        es.onopen = () => {};

        es.onclose = () => {
            es.onopen = () => {}
        };

        es.onerror = () => {
            es.onclose()
        };

        es.onmessage = message => {
            this.setState({
                message: JSON.parse(message.data)
            })
        };

        messageEs.onopen = () => {};

        messageEs.onclose = () => {
            messageEs.onopen()
        };

        messageEs.onerror = () => {
            messageEs.onclose()
        };

        messageEs.onmessage = message => {
            this.setState({
                message: JSON.parse(message.data)
            })
        };

        axios.get('/api/flowers')
            .then(res => {
                this.setState({
                    flowers: Object.entries(res.data)
                })
            })

    }

    handleClose(){
        this.setState({
            modalFlower: false,
            messagesModal: false
        })
    }

    handleToggleFlowerModal(){
        this.setState({
            modalFlower: true,
            messagesModal: false
        })
    }

    handleSend(value){
        if (value.action === 'flower'){
            es.send(JSON.stringify({
                action: 'flower',
                target: this.props.profile.id,
                message: value.text,
                type: value.id
            }));
        }
        else {
            messageEs.send(JSON.stringify({
                action: 'send',
                target: this.props.profile.id,
                message: value.message
            }))
        }
        this.setState({
            modalFlower: false,
            messagesModal: false
        })
    }

    handleMessagesModal(){
        this.setState({
            messagesModal: true,
            modalFlower: false
        })
    }


    render() {
        const {profile, trans} = this.props;
        const {flowers, modalFlower, messagesModal} = this.state;
        let count = 0;
        return (
            <div className="banner-search d-flex flex-row justify-content-around align-items-center">
                {modalFlower && flowers.length > 0 ? <TextAreaModal handleClose={this.handleClose} handleSend={this.handleSend} flowers={flowers} validate={trans.validate} /> : ""}
                {messagesModal ? <MessageModal handleClose={this.handleClose} handleSend={this.handleSend} validate={trans.validate} /> : ''}
                <div className="row marg-0 w-100">
                    <div className="col-md-4 col-sm-12">
                        <div className="row">
                            {profile.img ?
                                <div className={profile.img.length === 1 || profile.img.length === 0 ? "col-12" : "col-9"}>
                                {profile.img.length > 0 ? profile.img.map(img => {
                                    if (img.isProfile) {
                                        return (<ImageRenderer id={img.img} alt={"profil-img"}
                                                               className={"header-profile-img w-100"}/>)
                                    }
                                }) : ''}
                            </div> :
                            <div className="col-12 text-center">
                                {profile.isMan ?
                                    <img src={defaultMan} alt={"profile image"} className={"header-default-img "}/>
                                    :
                                    <img src={defaultWoman} alt={"profile image"} className={"header-default-img "}/>
                                }
                            </div>
                            }
                            {profile.img !== null && profile.img.length > 1 ?
                                <div className="col-3">
                                    {profile.img.length <= 3 ? profile.img.map((img, key) => {
                                        if (!img.isProfile && key <= 2) {
                                            return (<ImageRenderer id={img.img} alt={"profil-img"}
                                                                   className={"header-profile-img w-100"}/>)
                                        }
                                    }) : ''}
                                    {profile.img.length > 3 ?
                                        profile.img.map(img => {
                                            if (!img.isProfile && count < 1){
                                                count ++;
                                                return (
                                                    <ImageRenderer id={img.img} alt={"profile image"} className={"header-profile-img w-100"}/>
                                                )
                                            }
                                            if (!img.isProfile && count === 1){
                                                count ++;
                                                return (
                                                    <div className="position-relative marg-top-10">
                                                        <ImageRenderer id={img.img} alt={"profile image"} className={"header-profile-img w-100 marg-0"}/>
                                                        <div className="position-absolute header-absolute" data-toggle="modal"
                                                             data-target=".bd-profile-modal-lg"> + {profile.img.length - 3}</div>
                                                        <ImgModal img={profile.img} dataTarget={"bd-profile-modal-lg"}/>
                                                    </div>
                                                )
                                            }
                                        }) : '' }
                                </div>
                                : ''}
                        </div>
                    </div>
                    <div className="col-md-5 col-sm-12">
                        <h1>{profile.pseudo.toUpperCase()}</h1>
                        <h3>{profile.age} | {profile.canton} - {profile.city}</h3>
                        <h5>{profile.description}</h5>
                        <div className="d-flex flex-row justify-content-around">
                            <button className="btn btn-group btn-outline-danger marg-10" onClick={this.handleToggleFlowerModal}><FontAwesomeIcon icon={faSpa} className={"badged-icons marg-right-10"}/> {this.props.trans.sendFlower} </button>
                            <button className="btn btn-group btn-outline-dark marg-10" onClick={this.handleMessagesModal}> <FontAwesomeIcon icon={faComments} className={"badged-icons marg-right-10"} /> {this.props.trans.contact}</button>
                        </div>
                    </div>
                    <div className="col-md-3 col-sm-12">
                        <div className="row">
                            {profile.child ?
                                <div className="col-12 text-center">
                                    <h3>{profile.child.length} {trans.child}</h3>
                                </div>
                                : ''}
                                {profile.child ?
                                   profile.child.map((child, key) => {
                                       if (child.img){
                                           if(key <= 1){
                                               return(
                                                   <div className="col-6">
                                                       <ChildImageRenderer child={child} alt={"child image"} className={"header-profile-img w-100 position-relative"}/>
                                                       <div className="child-age">{child.age} {trans.yearsOld}</div>
                                                   </div>
                                               )
                                           }
                                       }
                                       else {
                                           return (
                                               <div className="col-6">
                                                   <img src={parseInt(child.sex) === 1 ? boyDefault : girlDefault} alt={"child image"} className={"header-profile-img w-100 position-relative"}/>
                                                   <div className="child-age">{child.age} {trans.yearsOld}</div>
                                               </div>
                                           )
                                       }
                                   })
                                    : ''}
                            {profile.child.length > 2 ?
                                <div className="col-12 text-center marg-top-10">
                                    <button className="btn btn-group btn-outline-dark marg-10" data-toggle="modal"
                                         data-target=".bd-child-modal-lg"> {trans.viewMore}</button>
                                    <ChildModal child={profile.child} dataTarget={"bd-child-modal-lg"} trans={trans}/>
                                </div> : '' }
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}