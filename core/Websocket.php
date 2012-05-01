<?php  
namespace core;
/**
 * protocol 10, chrome and ff 7+ (sha1 binart form 20 bytes!)
 * @see http://tools.ietf.org/html/draft-ietf-hybi-thewebsocketprotocol-10
 * Usage: $master=new WebSocket("localhost",12345);
 */
class WebSocket
{
    public $master;
    public $sockets = array();
    public $users   = array();
    public $debug   = null;
    const KEY = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
  
    public function __construct($address,$port,$debug=false)
    {       
        $this->debug = $debug;
        $this->master=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)     or die("socket_create() failed");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1)  or die("socket_option() failed");
        socket_bind($this->master, $address, $port)                    or die("socket_bind() failed");
        socket_listen($this->master,20)                                or die("socket_listen() failed");
        socket_set_block($this->master);
        $this->sockets[] = $this->master;
        $this->say("Server Started : ".date('Y-m-d H:i:s'));
        $this->say("Listening on   : ".$address." port ".$port);
        $this->say("Master socket  : ".$this->master."\n");

        while(true)
        {
            $changed = $this->sockets;    
            echo "\nWaiting for socket input....";
            echo "\nSockets loaded: ".print_r($this->sockets, true);
            if (false ===  socket_select($changed,$write=NULL,$except=NULL,NULL)) {
                echo "falló socket_select(), razón: " .
                    socket_strerror(socket_last_error()) . "\n";
            }
            foreach($changed as $socket)
            {
                if($socket==$this->master)
                {
                    $client=socket_accept($this->master);
                    if($client<0)
                    { 
                        $this->log("socket_accept() failed"); 
                        continue; 
                    }
                    else
                    { 
                        $this->connect($client); 
                    }
                }
                else{
                    $bytes = socket_recv($socket,$buffer,2048,0);
                    if($bytes==0){ 
                        $this->disconnect($socket); 
                    }
                    else
                    {
                        $user = $this->getuserbysocket($socket);
                        if(!$user->handshake)
                            $this->dohandshake($user,$buffer); 
                        else
                            $this->process($user,$this->unwrap($buffer)); 
                        $this->log('userHandshake: '.$user->handshake);
                    }
                }
            }
        }
    }

    public function process($user,$msg){
        echo __METHOD__.': '.$user.'-'.$message;
        /* Extend and modify this method to suit your needs */
        /* Basic usage is to echo incoming messages back to client */
        $this->send($user->socket,$msg);
    }

    public function send($client,$msg){ 
        $this->say("> ".$msg);
        $msg = $this->wrap($msg);
        socket_write($client,$msg,strlen($msg));
        $this->say("! ".strlen($msg));
    } 

    public function connect($socket){
        $user = new User();
        $user->id = uniqid();
        $user->socket = $socket;
        array_push($this->users,$user);
        array_push($this->sockets,$socket);
        $this->log('userid connected: '.$user->id);
        $this->log($socket." CONNECTED!");
        $this->log(date("d/n/Y ")."at ".date("H:i:s T"));
    }

    public function disconnect($socket){
        $found=null;
        $n=count($this->users);
        for($i=0;$i<$n;$i++){
          if($this->users[$i]->socket==$socket){ $found=$i; break; }
        }
        if(!is_null($found)){ array_splice($this->users,$found,1); }
        $index=array_search($socket,$this->sockets);
        socket_close($socket);
        $this->log($socket." DISCONNECTED!");
        if($index>=0){ array_splice($this->sockets,$index,1); }
    }

    /**
     *
     * @param type $user
     * @param type $buffer
     * @return type 
     */
    public function dohandshake($user,$buffer){
        $this->log("\nRequesting handshake...");
        $this->log($buffer);
        list($resource,$host,$key,$origin) = $this->getheaders($buffer);
        $this->log("Handshaking...");       
        $this->log(print_r(array($resource,$host,$key,$origin),true));
        $upgrade  = "HTTP/1.1 101 Switching Protocols\r\n" .
                    "Upgrade: websocket\r\n" .
                    "Connection: Upgrade\r\n" .
                    "Sec-WebSocket-Accept: " . base64_encode(sha1($key.self::KEY, true)) . "\r\n" .
                    "Sec-WebSocket-Protocol: superchat\r\n\r\n";
        socket_write($user->socket,$upgrade.chr(0),strlen($upgrade.chr(0)));
        $user->handshake=true;
        $this->log($upgrade);
        $this->log("Done handshaking...");
        return true;
    }
    
    /**
     *
     * @param type $req
     * @return type 
     */
    public function getheaders($req){
        $r=$h=$o=null;
        if(preg_match("/GET (.*) HTTP/"               ,$req,$match))    { $r=$match[1]; }
        if(preg_match("/Host: (.*)\r\n/"              ,$req,$match))    { $h=$match[1]; }
        if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/",$req,$match))     { $this->log("Key: ".$k=$match[1]); }
        if(preg_match("/Sec-WebSocket-Origin: (.*)\r\n/",$req,$match))  { $this->log("Origin: ".$o=$match[1]); }
        //if($match=substr($req,-8))                                      { $this->log("Last 8 bytes: ".$l8b=$match); }
        return array($r,$h,$k,$o);
    }

    public function getuserbysocket($socket){
        $found=null;
        foreach($this->users as $user){
          if($user->socket==$socket){ $found=$user; break; }
        }
        return $found;
    }

    public function     say($msg=""){ echo $msg."\n"; }
    public function     log($msg=""){ if($this->debug){ echo $msg."\n"; } }
    public function    wrap($msg=""){ return chr(0).$msg.chr(255); }
    public function  unwrap($msg=""){ return substr($msg,1,strlen($msg)-2); }

}

class User{
    public $id;
    public $socket;
    public $handshake;
}