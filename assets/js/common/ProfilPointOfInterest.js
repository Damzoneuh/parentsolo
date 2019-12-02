import React, {Component} from 'react';
import axios from 'axios';
import DropDownProfile from "./DropDownProfile";


export default class ProfilPointOfInterest extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        const {profile, trans} = this.props;
        return (
            <div className="rounded-more bg-light text-center marg-top-20 marg-bottom-20 pad-10">
                <h3>{trans["point.of.interest"]}</h3>
                <DropDownProfile content={profile.outings} title={trans.outing} img={null} imgClass={null}/>
                <DropDownProfile content={profile.cooking} title={trans.cooking} img={null} imgClass={null}/>
                <DropDownProfile content={profile.hobbies} title={trans.hobbies} img={null} imgClass={null}/>
                <DropDownProfile content={profile.sport} title={trans.sports} img={null} imgClass={null}/>
                <DropDownProfile content={profile.music} title={trans.music} img={null} imgClass={null}/>
                <DropDownProfile content={profile.movie} title={trans.movie} img={null} imgClass={null}/>
                <DropDownProfile content={profile.read} title={trans.reading} img={null} imgClass={null}/>
                <DropDownProfile content={profile.pet} title={trans.pets} img={null} imgClass={null}/>
            </div>
        );
    }


}