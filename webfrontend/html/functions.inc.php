<?php
    
    function sendUDP($data, $destIP, $destPort)
    {
    //start a new connection udp connection
    if(!($socket = socket_create(AF_INET, SOCK_DGRAM, 0))) {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
    	LOGCRIT("Couldn't create socket to send UDP Data: [$errorcode] $errormsg, terminating");
    	LOGEND("Processing terminated");
	    exit;
    }
    LOGDEB("Socket to send UDP Data created");

    //send udp datagram
    $numBytesSent = 0;
    $dataEnc = "";
    $dataEnc = mb_convert_encoding($data, "UTF-8");
    $numBytesSent = socket_sendto($socket, $dataEnc , strlen($dataEnc) , 0 , $destIP , $destPort);
    if( $numBytesSent == -1) {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        LOGERR("Couldn't send UDP Data: [$errorcode] $errormsg");
    }
    else{
        LOGDEB("UDP Data sent");
    }


    # close udp connection
    socket_close($socket);

    # ToDo: mybe check if all bytes in resultstr were sent
    }
         
?>