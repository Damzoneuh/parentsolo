import React, {Component} from 'react';
import axios from 'axios';
import ImageRenderer from "./ImageRenderer";
import Modal from '../common/Modal';
import defaultMan from '../../fixed/HommeDefaut.png';
import defaultWoman from '../../fixed/FemmeDefaut.png';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from "@fortawesome/fontawesome-svg-core";
import {faHeart, faComments, faSpa, faUser} from "@fortawesome/free-solid-svg-icons";
import ProfilShow from "./ProfilShow";
library.add(faHeart, faComments, faUser, faSpa);

export default class ShowAllProfile extends Component{
    constructor(props) {
        super(props);
        this.state = {
            search: this.props.search,
            profiles: [],
            modal: false,
            selectedProfile: {},
            trans: this.props.trans
        };

        this.handleFavorite = this.handleFavorite.bind(this);
        this.handleAcceptModal = this.handleAcceptModal.bind(this);
        this.handleCloseModal = this.handleCloseModal.bind(this);
        this.submitFavorite = this.submitFavorite.bind(this);
        this.handleSelectedProfile = this.handleSelectedProfile.bind(this);

    }

    componentDidMount(){
        if (this.state.search.length > 0){
            let profiles = [];
            this.state.search.map(s => {
                axios.get('/api/profile/' + s)
                    .then(res => {
                        profiles.push(res.data);
                        this.pushProfiles(profiles);
                    })
            })

        }
    }

    pushProfiles(value){
        this.setState({
            profiles: value
        })
    }

    handleFavorite(profile){
        if (profile.isFavorite){
            this.setState({
                selectedProfile: profile,
                action: 'favorite',
                modal: true
            })
        }
        else {
            this.submitFavorite(profile)
        }
    }

    submitFavorite(profile){
        let search = this.state.search;
        let profiles = [];
        axios.put('/api/favorite/' + profile.id)
            .then(res => {
                search.map(s => {
                    axios.get('/api/profile/' + s)
                        .then(res => {
                            profiles.push(res.data);
                            this.pushProfiles(profiles);
                            this.setState({modal: false})
                        })

                })
            })
            .catch(e => {
                window.location.href ='/shop'
            })
    }

    handleCloseModal(){
        this.setState({
            modal: false
        });
    }

    handleAcceptModal(){
        this.submitFavorite(this.state.selectedProfile)
    }

    handleSelectedProfile(id){
        this.props.handleShow(id)
    }

    render() {
        const {profiles, modal, trans} = this.state;
        if (profiles.length > 0){
            return (
                <div className="container-fluid">
                    <div className={!modal ? 'none' : ''}>
                        <Modal
                        text={trans.acceptFavorite}
                        type={'alert'}
                        handleClose={this.handleCloseModal}
                        handleAccept={this.handleAcceptModal}
                        validate={trans.accept}
                        cancel={trans.cancel}
                        />
                    </div>
                    <div className="row">
                        {profiles.map(profile => {
                            return (
                                <div className="col-lg-3 col-md-4 col-sm-12" key={profile.id}>
                                    <div className="text-center rounded-more bg-light marg-10 pad-10">
                                        <div className="profile-all-wrap">
                                            {profile.img && profile.img.length > 0 ? profile.img.map(img => {
                                                if (img.isProfile){
                                                    return (
                                                        <ImageRenderer id={img.img} className={"thumb-profile-img rounded-more"} alt={"profile image"}/>
                                                    )
                                                }
                                            }) : ''}
                                            {!profile.img && profile.isMan ? <img src={defaultMan} alt={"profile image"} className={"thumb-profile-img"}/> : ''}
                                            {!profile.img && !profile.isMan ? <img src={defaultWoman} alt={"profile image"} className={"thumb-profile-img"}/> : ''}
                                        </div>
                                        <h4 className="font-weight-bold">{profile.pseudo.toUpperCase()}</h4>
                                        {profile.age} | {profile.city} - {profile.canton}
                                        <div className="d-flex flex-row align-items-center justify-content-around marg-20">

                                            <button className="btn btn-outline-danger btn-lg btn-group-lg" onClick={() => this.handleSelectedProfile(profile.id)}>{this.props.trans.view}</button>

                                            <a onClick={() => this.handleFavorite(profile)}>
                                                <FontAwesomeIcon icon={'heart'} color={profile.isFavorite ? 'rgba(255,0,0,0.8)' : 'rgba(0,0,0,0.3)'} className={'font-size-30'}/>
                                            </a>
                                            <FontAwesomeIcon icon={'spa'} color={'rgba(0,0,0,0.3)'} className={'font-size-30'}/>
                                            <FontAwesomeIcon icon={'comments'} color={'rgba(0,0,0,0.3)'} className={'font-size-30'}/>
                                        </div>
                                    </div>
                                </div>
                            )
                        })}
                    </div>
                </div>
            );
        }

        else {
            return (
                <div>

                </div>
            )
        }

    }

}