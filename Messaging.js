const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const mysql = require('mysql');
const axios = require('axios');
let fs = require('fs');

let connection = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_DATABASE,
    port: process.env.DB_PORT
});

// const wss = new WS.Server({noServer: true});
// wss.on('connection', function connection(req) {
//     console.log(req)
// });
//


connection.connect();
const app = express();
const server = http.createServer({}, app);
const events = require('events');
let eventEmitter = new events.EventEmitter();

const wss = new WebSocket.Server({server});

wss.on('connection', (ws, req) => {

    let path = req.url;
    let idMap = [];
    let messages = [];

    let notification = (target, type, content) => {
        target.send(JSON.stringify({'type': type, 'content': content, redirect: false}));
    };

    if (path.search('/c') === 0) {
        ws.id = path.substring(3);
        idMap[ws.id] = ws;
        let auth = null;
        axios.get("https://parentsolo.disons-demain.be/api/user/" + ws.id)
            .then(res => {
                if (res.data.isSub){
                    auth = true;
                }
            })
            .catch(e => console.log(e));
        connection.query('SELECT * FROM messages WHERE message_to=' + ws.id + ' OR message_from=' + ws.id + ' ORDER BY id ASC', (err, rows, fields) => {
            if (!err && rows.length > 0) {
                rows.map((row) => {
                    messages.push(row);
                });
                ws.send(JSON.stringify(messages));
                messages = [];
            }
            else {
                ws.send(JSON.stringify(['no messages']));
            }
        });

        ws.on('message', (message) => {
            message = JSON.parse(message);
            if (message.action === 'send' && auth) {
                connection.query('INSERT INTO messages (message_from, message_to, content, is_read) VALUES (' + ws.id + ', ' + message.target + ', "' + encodeURI(message.message) + '", false)');
                connection.query('SELECT * FROM messages WHERE message_to=' + ws.id + ' OR message_from=' + ws.id + ' ORDER BY id ASC', (err, rows, fields) => {
                    if (!err){
                        ws.send(JSON.stringify(rows))
                    }
                    else {
                        console.log(err)
                    }
                });

                let dest = idMap[message.target];
                if (dest) {
                    connection.query('SELECT * FROM messages WHERE message_from=' + ws.id + ' AND message_to=' + dest.id, (err, rows) => {
                        if (rows.length > 0) {
                            rows.map((row) => {
                                messages.push(row);
                            });
                            dest.send(JSON.stringify(messages));
                            messages = [];
                            axios.get("https://parentsolo.disons-demain.be/api/trans/newmessage")
                                .then(res => {
                                    notification(dest, 'message', res.data.trans);
                                })

                        }
                    });
                } else {
                    axios.post('https://parentsolo.disons-demain.be/api/messages/mailer', {
                        "content": message.message,
                        "target": message.target,
                        "from": ws.id
                    }).then(res => {
                        connection.query('SELECT * FROM messages WHERE message_to=' + ws.id +
                            ' OR message_from=' + ws.id + ' ORDER BY id ASC', (err, rows, fields) => {
                            ws.send(JSON.stringify(rows))
                        });
                    })
                }
            }
            else {
                axios.get("https://parentsolo.disons-demain.be/api/trans/notsub")
                    .then(res => {
                        notification(ws, 'error', res.data.trans);
                    })

            }

            if (message.action === 'read'){
                connection.query('UPDATE messages SET is_read=true WHERE message_to=' + ws.id + ' AND message_from=' + message.target);
                connection.query('SELECT * FROM messages WHERE message_to=' + ws.id + ' OR message_from=' + ws.id + ' ORDER BY id \'ASC\'', (err, rows, fields) => {
                    ws.send(JSON.stringify(rows))
                })
            }
        });


        ws.on('close', (ws) => {
            delete idMap[ws.id];
        })
    }

    if (path.search('/f') === 0){
        ws.id = path.substring(3);
        idMap[ws.id] = ws;
        let dest = null;
        let payload = {};

        connection.query('SELECT * FROM flower_received where target_id=' + ws.id + ' and is_read=' + false, (err, rows, fields) => {
            payload.redirect = false;
            payload.flowers = rows;
            ws.send(JSON.stringify(payload));
        });

        ws.on('message', (message) => {
            if (JSON.parse(message).action === 'flower'){
                axios.get('https://parentsolo.disons-demain.be/api/auth/flowers/' + ws.id)
                    .then(res => {
                        if (res.data.success){
                            let data = JSON.parse(message);
                            connection.query('INSERT INTO flower_received (target_id, sender_id, message, flower_id, is_read) ' +
                                'VALUES ('+ data.target +', ' + ws.id + ', "' + encodeURI(data.message) + '", ' + data.type + ', false)', (error) => {
                                console.log(error);
                            });
                            notification(ws, 'success', res.data.content);
                            dest = idMap[message.target];
                            if (dest){
                                let messageToSend = {
                                    'type': 'success',
                                    'content': res.data.content,
                                    'redirect' : false
                                };
                                dest.send(JSON.stringify(messageToSend));
                            }
                            else {
                                axios.post('https://parentsolo.disons-demain.be/api/mailer/notification', {type: data.action, target: data.target, sender: ws.id})
                                    .then(res => {
                                        console.log(res.data);
                                    })
                            }
                        }
                        else {
                            ws.send(JSON.stringify({'redirect': 'shop'}))
                        }
                    })

            }
        })
    }
});

server.listen(80, () => {
    console.log('working')
});
