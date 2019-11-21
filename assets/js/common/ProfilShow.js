import React, {Component} from 'react';
import HeaderShowProfile from "./HeaderShowProfile";

export default class ProfilShow extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        const {profile, trans} = this.props;
        console.log(profile);
        if (Object.entries(profile).length > 0){
            return(
                <HeaderShowProfile profile={this.props.profile} trans={trans} />
            )
        }
    }


}