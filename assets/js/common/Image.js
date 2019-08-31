import React, {Component} from 'react'
import axios from 'axios';

export default class Image extends Component{
    constructor(props){
        super(props);
        this.state = {
            isImgLoaded: false,
            img: null,
            name: null,
            preview: null,
            resize: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    async handleChange(e){
        if (e.target.type === "file"){
            this.setState({
                img: e.target.files[0]
            });
            let resize = await this.setPreviewState(e.target.files[0]);
            let fr = new FileReader();
        }
        else {
            this.setState({
                [e.target.name]: e.target.value
            })
        }
        axios.get(this.state.preview)
            .then(res => {
                console.log(res.data)
            })
    }

     setPreviewState(file) {
        return new Promise(resolve => {
            this.setState({
                preview: URL.createObjectURL(file)
            });
            resolve('done')
        });
    }

    handleSubmit(){
        event.preventDefault();
        console.log(this.state.img);
        let data = new FormData();
        data.append('file', this.state.img);
        data.append('name', this.state.name);
        axios.post('/api/image', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
            .then(res => {
                console.log(res.data)
            })
    }

    render() {
        return (
            <div>
                <form onChange={this.handleChange} onSubmit={this.handleSubmit} method="post">
                    <div className="form-group">
                        <div className="row">
                            <div className="col-6">
                                <label htmlFor="file">Image</label>
                                <input type="file" className="form-control-file" id="file" name="file" />
                            </div>
                            <div className="col-6">
                                {this.state.preview ? <img src={this.state.preview} className="w-25"/> : ''}
                            </div>
                        </div>
                    </div>
                    <div className="form-group">
                        <label htmlFor="name">File name </label>
                        <input type="text" name="name" className="form-control"/>
                    </div>
                    <div className="form-group">
                        <button className="btn btn-primary btn-group">Submit</button>
                    </div>
                </form>
            </div>
        );
    }

}