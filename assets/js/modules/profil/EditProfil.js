import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import HeaderEditProfile from "./HeaderEditProfile";
import UnderNav from "../../common/UnderNav";

export default class EditProfil extends Component{
    constructor(props) {
        super(props);

    }


    render() {
        return (
            <div>
                <UnderNav data={"edit-profile"}/>
                <HeaderEditProfile />
            </div>
        );
    }
}

ReactDOM.render(<EditProfil/>, document.getElementById('edit-profile'));