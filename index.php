<?php
require_once "config.php";
require_once "util.php";

foreach(glob("Plugins/*") as $plugin){
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

$data = $updateData["callback_query"]["data"];
$from_id = $messageData["from"]["id"];
$from_name = $messageData["from"]["first_name"] . $messageData["from"]["last_name"];
$from_username = $messageData["from"]["username"];

// media
$sticker = $messageData["sticker"];
$sticker_id = $messageData["sticker"]["file_id"];
$voice = $messageData["voice"];
$voice_id = $messageData["voice"]["file_id"];
$file = $messageData["document"];
$file_id = $messageData["document"]["file_id"];
$audio = $messageData["audio"];
$audio_id = $messageData["audio"]["file_id"];
$video = $messageData["video"];
$video_id = $messageData["video"]["file_id"];
$contact = $messageData["contact"];
$contact_id = $messageData["contact"]["file_id"];
$photo = $messageData["photo"];
$photo_id = $messageData["message"]["photo"][0]["file_id"];


if(isset($messageData["text"])){
    //if message came late more than 15 second, exit
    if($messageTime+15 < time()){
        exit();
    }
    

    //if text is a bot command
    if(preg_match("/^\/.*(@".$botUsername.")*/",$messageText)){
        //if command for other bot, exit
        if(preg_match("/^\/\w*@(?!".$botUsername.")/",$messageText)){
            exit();
        }

        //splitting text to command and argument
        $messageText = explode(" ",$messageText,2);
        $command = $messageText[0];
        $arg = isset($messageText[1]) ? $messageText[1] : "";

        //removing "/" character and bot username from command
        $command = str_replace("@$botUsername","",substr($command,1));

        //if command function exists, execute that function
        if(function_exists("command_$command")){
            call_user_func("command_$command",$arg);
        }
    }elseif(preg_match("/#([^_\s]+)_?([\S_]*) ?(.*)/",$messageText,$match)){
        $command = $match[1];
        $arg = str_replace("_"," ",$match[2]);
        $arg1 = $match[3];
        $arg = trim($arg." ".$arg1);
        if(function_exists("command_$command")){
            call_user_func("command_$command",$arg);
        }
    }
}
