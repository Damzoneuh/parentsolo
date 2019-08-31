import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import 'bootstrap/dist/css/bootstrap.css';
import '../../../sass/global.scss';
import SelectPayment from "./SelectPayment";
import CardEntries from "./CardEntries";
import Logger from "../../common/Logger";
import axios from 'axios';
import KnowCards from "./KnowCards";

export default class Payment extends Component{
   constructor(props){
       super(props);
       let doc = document.getElementById('payment');
       let settings = doc.dataset.settings;
       let token = doc.dataset.token;
       this.state = {
           isLoaded: false,
           settings: JSON.parse(settings),
           token: token,
           tab: 1,
           message: {
               message: null,
               type: null
           },
           cards: []
       };
       axios.get('/api/payment/profil')
           .then(res => {
               this.setState({
                   cards: res.data
               });
           });
       this.tabHandler = this.tabHandler.bind(this);
       this.loggerHandler = this.loggerHandler.bind(this);
   }

   tabHandler(tab){
       this.setState({
           tab: tab
       })
   }

   loggerHandler(data){
       this.setState({
           message: {
               type: data.type,
               message: data.message
           }
       })
   }


    render() {
       const {settings, message, tab, token, cards} = this.state;
       if (tab === 1) {
           return (
               <div>
                   <Logger message={message.message} type={message.type}/>
                   <SelectPayment handler={this.tabHandler}/>
                   {cards.length > 0 ? <KnowCards cards={cards} token={token} settings={settings} logger={this.loggerHandler}/> : ''}
               </div>
           );
       }
       if (tab === 2){
           return (
               <div>
                   <Logger message={message.message} type={message.type}/>
                   <CardEntries handler={this.tabHandler} token={token} settings={settings} logger={this.loggerHandler}/>
               </div>
           )
       }
    }

}

ReactDOM.render(<Payment/>, document.getElementById('payment'));