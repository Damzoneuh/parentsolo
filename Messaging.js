const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const mysql = require('mysql');
import axios from 'axios';
let connection = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_DATABASE,
    port: process.env.DB_PORT
});
connection.connect();
const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({server});
const idMap = [];

wss.on('connection', (ws, req) => {
    let url = require('url').parse(req.url);
    let path = url.pathname;
    if (path.search('/c') === 0){
        ws.id = path.substring(3);
        idMap[ws.id] = ws;
        connection.query('SELECT * FROM messages WHERE message_to=' + ws.id + ' ORDER BY \'DESC\' LIMIT 10', (err, rows, fields) => {
            if (!err && rows.length > 0){
                //console.log(rows);
                rows.map((row) => {
                    ws.send(JSON.stringify(row));
                });
            }
            else {
                //console.log(err);
                ws.send('nothing here')
            }
        });
        ws.on('message', (message) => {
            message = JSON.parse(message);
            if (message.action === 'send'){
                connection.query("INSERT INTO messages (message_from, message_to, content) VALUES (" + ws.id + ", " + message.target + ", '" + message.message + "')");
                let dest = idMap[message.target];
                if (dest){
                    dest.send(JSON.stringify(message))
                }
                else {
                    //TODO add ENV URL, add mailer message route, add component react
                    axios.post('https://parentsolo.backndev.fr/api/messages/mailer', {
                        "content" : message.content,
                        "target": message.target,
                        "from": ws.id
                    }).then(res => {
                        console.log('message sent')
                    });
                }
            }
        });
        ws.on('close', (ws) => {
            delete idMap[ws.id];
        })
    }
});

server.listen(5050, () => {
    console.log('working')
});