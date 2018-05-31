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
$messageText = $messageData["text"];


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


           //splitting text to command and argument
        $explodetext = explode(" ",$messageText,2);
        $command = $explodetext[0];
        $arg = isset($explodetext[1]) ? $explodetext[1] : "";

        //removing "/" character and bot username from command
        $command = str_replace("@$botUsername","",substr($command,1));


        if(function_exists("command_$command")){
            call_user_func("command_$command",$arg);
        }
    }
