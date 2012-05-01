<html>
<head>
<title>WebSocket</title>

<style>
 html,body{font:normal 0.9em arial,helvetica;}
 #log {width:440px; height:200px; border:1px solid #7F9DB9; overflow:auto;}
 #msg {width:330px;}
</style>

<script>
var socket;

function init(){
  
    try{
    
        var websocketHost = "ws://192.168.247.131:8080/";

        if ("WebSocket" in window) {
            socket = new WebSocket(websocketHost);
        }
        else if ("MozWebSocket" in window) {
            socket = new MozWebSocket(websocketHost);
        }
        if(!socket)
            log('your browser is not compatible');
    
        log('WebSocket - status '+socket.readyState);
        socket.onopen    = function(v){
            log("Welcome - status "+this.readyState); 
        };
        socket.onmessage = function(v){ log("Received: "+v.data); };
        socket.onclose   = function(v){ log("Disconnected - status "+this.readyState); };
    }
    catch(ex){ log(ex); }
    $("msg").focus();
}

function send(){
    var txt,msg;
    txt = $("msg");
    var msg = txt.value;
    if(!msg){ alert("Message can not be empty"); return; }
    txt.value="";
    txt.focus();
    try{ 
        var sendLoop = setInterval(function() {
        if (socket.bufferedAmount == 0)
            socket.send(msg); 
            log('buffered amount: '+socket.bufferedAmount);
            log('Sent: '+msg); 
            clearInterval(sendLoop);
        }, 50);
    } 
    catch(ex){ 
      log(ex); 
    }
}
function quit(){
  log("Goodbye!");
  socket.close();
  socket=null;
}

// Utilities
function $(id){ return document.getElementById(id); }
function log(msg){ $("log").innerHTML+="<br>"+msg; }
function onkey(event){ if(event.keyCode==13){ send(); } }
</script>

</head>
<body onload="init()">
 <h3>WebSocket v2.00</h3>
 <div id="log"></div>
 <input id="msg" type="textbox" onkeypress="onkey(event)"/>
 <button onclick="send()">Send</button>
 <button onclick="quit()">Quit</button>
 <div>Commands: hello, hi, name, age, date, time, thanks, bye</div>
</body>
</html>
