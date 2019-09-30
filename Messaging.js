const express = require('express');
const http = require('https');
const {createServerFrom} = require('wss');
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
connection.connect();
const app = express();
const server = http.createServer({
    key: fs.readFileSync('cle-privee.key'),
    cert: fs.readFileSync('certificat.pem'),
    ca: [fs.readFileSync('core_ca.pem')],
    passphrase: 'platine74',
    secureProtocol: true
}, app);
const idMap = [];
let messages = [];
createServerFrom(server, function connectionListener (ws){
    let path = ws.upgradeReq.url;
    if (path.search('/c') === 0) {
        ws.id = path.substring(3);
        idMap[ws.id] = ws;
        connection.query('SELECT * FROM messages WHERE message_to=' + ws.id + ' OR message_from=' + ws.id + ' ORDER BY \'ASC\'', (err, rows, fields) => {
            if (!err && rows.length > 0) {
                rows.map((row) => {
                    messages.push(row);
                });
                ws.send(JSON.stringify(messages));
                messages = [];
            }
        });
        ws.on('message', (message) => {
            message = JSON.parse(message);
            if (message.action === 'send') {
                connection.query("INSERT INTO messages (message_from, message_to, content, is_read) VALUES (" + ws.id + ", " + message.target + ", '" + encodeURI(message.message) + "', false)");
                connection.query('SELECT * FROM messages WHERE message_to=' + ws.id + ' OR message_from=' + ws.id + ' ORDER BY \'ASC\'', (err, rows, fields) => {
                    ws.send(JSON.stringify(rows))
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
                            ' OR message_from=' + ws.id + ' ORDER BY \'DESC\' LIMIT 10', (err, rows, fields) => {
                            ws.send(JSON.stringify(rows))
                        });
                    })
                }
            }
            if (message.action === 'read'){
                connection.query('UPDATE messages SET is_read=true WHERE message_to=' + ws.id + ' AND message_from=' + message.target);
                connection.query('SELECT * FROM messages WHERE message_to=' + ws.id + ' OR message_from=' + ws.id + ' ORDER BY \'ASC\'', (err, rows, fields) => {
                    ws.send(JSON.stringify(rows))
                })
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