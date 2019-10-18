import React, {Component} from 'react';
import axios from 'axios';
import ImageRenderer from "../../common/ImageRenderer";
import defaultMan from '../../../fixed/HommeDefaut.png';
import defaultWoman from '../../../fixed/FemmeDefaut.png';


//TODO mettre les liens vers les profils

export default class LastProfiles extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            ids: [],
            userDetails: [],
            trans: []
        }
    }

    componentDidMount(){
        let limit = 5;
        if (window.matchMedia('(max-width: 752px)').matches){
            limit = 3;
        }
        axios.get('/api/last/profile/' + limit)
            .then(res => {
                this.setState({
                    ids: res.data,
                });
                res.data.map(data => {
                   axios.get('/api/profile/light/' + data.id)
                        .then(user => {
                            this.pushUsers(user.data)
                        })
                });
            });
        axios.get('/api/trans/search')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            });
    }

    pushUsers(user){
        let previous = this.state.userDetails;
        previous.push(user);
        this.setState({
            userDetails: previous,
            isLoaded: true
        })
    }

    render() {
        const {isLoaded, userDetails, trans} = this.state;
        if (isLoaded){
           return (
               <div className="row text-center">
                   <div className="marg-10">
                       {trans.lastProfileTitle.toUpperCase()}
                       <a className="plus text-danger marg-10">+</a>
                   </div>
                   <div className="col-12">
                       <div className="d-flex flex-row justify-content-between align-items-center" >
                           {userDetails.map(user => {
                               if (user.img !== null){
                                   return (
                                       <a className="last-profile-link">
                                            <ImageRenderer alt={"profile"} className={"testimony-img red-border position-relative"} id={user.img}/>
                                            <div className="position-absolute bg-danger hover-img">
                                                <div className="text-white text-profile-hover">
                                                    {user.age}<br/>
                                                    {user.canton}<br/>
                                                    {/*{user.child}*/}
                                                </div>
                                            </div>
                                           <div className="text-center">
                                               {user.pseudo.toUpperCase()}
                                           </div>
                                       </a>
                                   )
                               }
                               if (user.isMan){
                                   return (
                                       <a className="last-profile-link">
                                           <img src={defaultMan} alt={"profil"} className={"testimony-img red-border position-relative"}/>
                                           <div className="position-absolute bg-danger hover-img">
                                               <div className="text-white text-profile-hover">
                                                   {user.age}<br/>
                                                   {user.canton}<br/>
                                                   {/*{user.child}*/}
                                               </div>
                                           </div>
                                           <div className="text-center">
                                               {user.pseudo.toUpperCase()}
                                           </div>
                                       </a>
                                   )
                               }
                               else {
                                   return (
                                       <a className="last-profile-link">
                                           <img src={defaultWoman} alt={"profil"} className={"testimony-img red-border position-relative"}/>
                                           <div className="position-absolute bg-danger hover-img">
                                               <div className="text-white text-profile-hover">
                                                   {user.age}<br/>
                                                   {user.canton}<br/>
                                                   {/*{user.child}*/}
                                               </div>
                                           </div>
                                           <div className="text-center">
                                               {user.pseudo.toUpperCase()}
                                           </div>
                                       </a>
                                   )
                               }
                           })}
                       </div>
                   </div>
               </div>
           )
        }
        else {
            return (
                <div>

                </div>
            );
        }
    }

}