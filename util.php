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
