
let socket = require("socket.io"),
    express = require("express"),
    app     = express(),
    server  = require("http").createServer(app),
    io      = socket.listen(server),
    port    = process.env.port || 3000;

server.listen(port, function () { 
    console.log(port);
});

/*
let _http   = require("http"),
    _https  = require("https"),
    express = require("express")(),
    http    = _http.createServer(express),
    https   = _https.createServer(express),
    io      = require("socket.io")(http);
    
    http.listen(process.env.SERVER_HTTP_PORT);
    https.listen(process.env.SERVER_HTTPS_PORT);
*/
io.on("error", function (data) {
    console.log(data);
});
io.on("connection", function (_socket) { 

    _socket.on("user_online", function (username) { 
        io.emit("user_online", username);
    });

    _socket.on("kirim_pesan", function (pesan) { 
        io.emit("kirim_pesan", pesan);
    });

    _socket.on("edit_pesan", function (pesan) { 
        io.emit("edit_pesan", pesan);
    });
    
    _socket.on("delete_pesan", function (pesan) { 
        io.emit("delete_pesan", pesan);
    });

    _socket.on("is_ngetik", function (pesan) { 
        io.emit("is_ngetik", pesan);
    });

    _socket.on("disconnect", function () { 
        console.log("disconnect");
    });
});