import React, {Component} from 'react';
import axios from 'axios';
import GroupParticipation from "../../common/GroupParticipation";
import ProfilPointOfInterest from "../../common/ProfilPointOfInterest";
import Adsense from "../../common/Adsense";
import diaryImg from "../../../fixed/Agenda.png";

export default class ProfilBody extends Component{
    constructor(props) {
        super(props);
        this.state = {
            trans: null,
            diary: null
        }
    }

    componentDidMount(){
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            });
        axios.get('/api/diary')
            .then(res => {
                this.setState({
                    diary: res.data
                })
            })
    }


    render() {
        const {trans, diary} = this.state;
        const {profile} = this.props;
        if (trans && diary){
            return (
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-lg-3 col-sm-12">
                            <div>
                                <GroupParticipation profile={profile.id} trans={trans}/>
                                <ProfilPointOfInterest trans={trans} profile={profile}/>
                            </div>
                        </div>
                        <div className="col-lg-6 col-sm-12">
                            <div className="rounded-more bg-light marg-top-20 marg-bottom-20 pad-10">
                                <h3>{trans.personality}</h3>
                                <div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans["relationship.search"]} :</div> <div>{profile.personality.relation}</div></div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans.temperament} :</div> <div>{profile.personality.temperament}</div></div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans["childs.wanted"]} :</div> <div>{profile.personality.wantedChild}</div></div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans["nationality"]} :</div> <div>{profile.personality.nationality}</div></div>
                                </div>
                            </div>

                            <div className="rounded-more bg-light marg-top-20 marg-bottom-20 pad-10">
                                <h3>{trans.personality}</h3>
                                <div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans["relationship.search"]} :</div> <div>{profile.personality.relation}</div></div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans.temperament} :</div> <div>{profile.personality.temperament}</div></div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans["childs.wanted"]} :</div> <div>{profile.personality.wantedChild}</div></div>
                                    <div className="d-flex justify-content-between align-items-center"><div>{trans["nationality"]} :</div> <div>{profile.personality.nationality}</div></div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-3 col-sm-12">
                            <Adsense />
                            <div className="diary-wrap">
                                <div className="flex flex-row justify-content-between marg-10">
                                    <h3>{diary.diary.toUpperCase()}</h3>
                                    {/*<img src={diaryImg} alt="diary" className="diary-img" />*/}
                                </div>
                                {trans["discover.text"]}
                                <div className="text-center">
                                    <a href="#" className="btn btn-group btn-outline-light marg-top-10">{trans.discover}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }

        else {
            return (
                <div>

                </div>
            );
        }
    }


}