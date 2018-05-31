<?php

function apiUrl($command){
    global $TOKEN;
    return "https://api.telegram.org/bot$TOKEN/$command";
}


// sendMessage function
function sendMessage($text,$replyMessage=true,$replyMarkup=false,$toChat=false){
    global $messageId,$chatId;

    //if text is not specified, return
    if(empty($text)){
        return false;
    }

    $data = array(
        "text" => $text,
        "parse_mode" => "Markdown",
        "disable_web_page_preview" => TRUE,
    );

    //if replyMessage is an integer(message id)
    if(is_int($replyMessage)){
        $data["reply_to_message_id"] = $replyMessage;
    }elseif($replyMessage){
        //if just true, reply to received message
        $data["reply_to_message_id"] = $messageId;
    }

    //reply markup
    if($replyMarkup){
        $data["reply_markup"] = json_encode($replyMarkup);
    }
    //if toChat is not specified, send message to received chat id
    if(!$toChat){
        $data["chat_id"] = $chatId;
    }else{
        $data["chat_id"] = $toChat;
    }

    $result = sendCommand("sendMessage",$data);
    return $result;
}


// sendCommand function
function sendCommand($command,$data=false,$fullReturn=false){
    //if data is not given, use GET method
    if(!$data){
        $url = apiUrl($command);
        $result = file_get_contents($url);
        $result = json_decode($result,true);
        return $fullReturn ? $result : $result["result"];
    }
    //else, use POST with curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, apiUrl($command));
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


// database connect function
function databaseConnect(){
    global $databaseHost,$databaseName,$databaseUsername,$databasePassword;

    $database = new PDO("mysql:host=$databaseHost;dbname=$databaseName",$databaseUsername,$databasePassword);
    return $database;
}
