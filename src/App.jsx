import React, { Component }  from "react"
import './App.css'

//URL для асинхронного запроса при успешной оплате
const acync_url = "http://"+location.host+"/payments/asyncRequest/"

const service = {
    sendData : (data) => {
        data = {
            "appointment":  data.appointment,
            "card_Number":  data.card_Number,
            "total":        data.total,
            "url":          acync_url          
        }
        let requestOptions = {
            method: 'POST',
            headers:{'content-type': 'application/json'},
            body: JSON.stringify(data)
        }
        return new Promise(function(resolve, reject) {
            fetch('/register', requestOptions)
                .then(service.handleResponse)
                .then(function (response){
                    resolve(response);
                })
                .catch(function(error) {
                    resolve(error); 
                });
        });
    },
    getData : (data) => {
        let requestOptions = {
            method: 'GET',
            headers:{'content-type': 'application/json'}
        }
        let fromDate = data.fromDate.length>0?'fromDate='+data.fromDate:""
        let toDate = data.toDate.length>0?'toDate='+data.toDate:""
        return new Promise(function(resolve, reject) {
            fetch('/getData?'+fromDate+'&'+toDate,requestOptions)
                .then(service.handleResponse)
                .then(function (response){
                    resolve(response);
                })
                .catch(function(error) {
                    resolve(error); 
                });
        });
    },
    handleResponse : (response) => { 
        return response.text().then(text => {
            let data = JSON.parse(text);
            if (!response.ok) {
                return Promise.reject(data);
            }
            return data;
        });
    }
}

const Row = (props) => {
    return (
        <div className="row">
            <label>{props.label}</label>
            <input onChange={props.handleChange} name={props.name} required = {props.required} type={props.inputType}/>
        </div>
    )
}

class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            link: "",
            error: "",
            appointment: "",
            card_Number: "",
            total: "",
            loading: false,
            status: "",
            getData_error: "",
            data: "",
            fromDate: "",
            toDate: ""
        }
        this.send = this.send.bind(this)
        this.getData = this.getData.bind(this)
        this.handleChange = this.handleChange.bind(this)
    }

    getData(){
        this.setState({loading:true})
        //отправка
        let it = this
        service.getData(this.state).then((res) => {
            if (res.status=='error') it.setState({data: "", getData_error:res.result, status:res.status, loading:false})
            else it.setState({data: res.result, getData_error:"", status:res.status, loading:false})
        });
    }
    send(){
        this.setState({loading:true})
        //отправка
        let it = this
        service.sendData(this.state).then((res) => {
            if (res.status=='error') it.setState({link: "", error:res.result, status:res.status, loading:false})
            else it.setState({link: res.result, error:"", status:res.status, loading:false})
        });
    }


    handleChange(){
        this.setState({[event.target.name]: event.target.value})
    }


    render(){
        return (
            <div className="text-center">
                <div className="content">
                    <h2>Форма оплаты</h2>
                    <span>Отправьте форму для сохранения данных, в случае успешной отправки и валидности введённых данных вы получите ссылку на детали оплаты</span>
                    <div className="container">
                        <div className="card">
                            <Row required  handleChange={this.handleChange} name="appointment" label="Назначение платежа" inputType="text"/>
                            <Row required  handleChange={this.handleChange} name="card_Number" label="Номер карты" inputType="text"/>
                            <Row required  handleChange={this.handleChange} name="total" label="Сумма платежа" inputType="text"/>
                        </div>
                    </div>
                    <span>* Тип полей и валидацию не настраивал специально для свободной отправки любых данных для тестирования back части</span>
                    <div className="row">
                        <button disabled={this.state.loading} onClick={this.send}>{this.state.loading?"Загрузка":"Оплатить"}</button>
                    </div>
                    <div className="row">
                        {this.state.link&&<span style={{color: "blue"}}><font style={{color: "black"}}>Оплата произведена успешно:</font> {<a target="_blank" href={this.state.link}>ссылка на детали оплаты</a>}</span>}
                        {this.state.error&&<span style={{color: "#f91919"}}>{this.state.error}</span>}
                    </div>
                    <div className="row">
                        <span style={{borderBottom: "1px solid #bcbcbc",marginBottom: "10px"}}>Блок для проверки метода для получения всех записей из БД за указанный период</span>
                        <div style={{display:"inline-flex"}}>
                            <Row required  handleChange={this.handleChange} name="fromDate" label="от" inputType="datetime-local"/>
                            <Row required  handleChange={this.handleChange} name="toDate" label="до" inputType="datetime-local"/>
                        </div>
                        <button disabled={this.state.loading} onClick={this.getData}>{this.state.loading?"Загрузка":"Получить список"}</button>
                    </div>
                    <div className="row">
                        {this.state.data&&
                            <div>
                                <span>{this.state.data.length>0?"Список транзакций:":"Нет транзакций в указанный период"}</span>
                                <ul>
                                    {this.state.data.map((value,index)=>{
                                        return <li key={index}>
                                                    <div><span>Название платежа </span><span>{value.appointment}</span></div>
                                                    <div><span>Номер карты </span><span>{value.card_Number}</span></div>
                                                    <div><span>Сумма платежа </span><span>{value.total}</span></div>
                                                    <div><span>Дата транзакции </span><span>{value.date}</span></div>
                                                </li>
                                    })}
                                </ul>
                            </div>}
                        <span style={{color: "#f91919"}}>{this.state.getData_error}</span>
                    </div>
                </div>
            </div>
        )
    }
}

export default App