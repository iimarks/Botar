<?php

require_once "config.php";
require_once "util.php";

foreach(glob("Commands/*") as $plugin){
    include_once $plugin;
}

$update = file_get_contents("php://input");
//if this file accessed via browser or terminal(empty input), exit
if(empty($update)){
    exit("Error, don't open this file directly from your browser or terminal");
}

$updateData = json_decode($update,true);
$messageData = isset($updateData["callback_query"]) ? $updateData["callback_query"]["message"] : $updateData["message"];
$messageTime = $messageData["date"];
$chatId = $messageData["chat"]["id"];
$messageId = $messageData["message_id"];

if(isset($updateData["callback_query"])){
    $data = $updateData["callback_query"]["data"];
    if($data == "zero"){
        exit();
    }

    $data = explode("_",$data,2);

    if(function_exists("cq_$data[0]")){
        call_user_func("cq_$data[0]",$data[1]);
    }
}elseif(isset($messageData["text"])){
    //if message came late more than 15 second, exit
    if($messageTime+15 < time()){
        exit();
    }

    $messageText = $messageData["text"];

    //if text is a bot command
    if(preg_match("/^[\/#!].*(@".$botUsername.")*/",$messageText)){
        //if command for other bot, exit
        if(preg_match("/^\/.*@(?!".$botUsername.")/",$messageText)){
            exit();
        }

        //splitting text to command and argument
        $messageText = explode(" ",$messageText,2);
        $command = $messageText[0];
        $arg = isset($messageText[1]) ? $messageText[1] : "";

        //removing "/" character and bot username from command
        $command = str_replace("@$botUsername","",substr($command,1));
