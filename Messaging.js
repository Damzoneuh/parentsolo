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

const wss = new WebSocket.Server({server});

wss.on('connection', (ws, req) => {

    let path = req.url;
    let idMap = [];
    let messages = [];

    if (path.search('/c') === 0) {
        ws.id = path.substring(3);
        idMap[ws.id] = ws;

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
            if (message.action === 'send') {
                connection.query("INSERT INTO messages (message_from, message_to, content, is_read) VALUES (" + ws.id + ", " + message.target + ", '" + encodeURI(message.message) + "', false)");
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
                        }
                    });
                } else {
                    axios.post('https://parentsolo.backndev.fr/api/messages/mailer', {
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
});

server.listen(5000, () => {
    console.log('working')
});
