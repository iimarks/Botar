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
// sendMedia function 
function sendMedia($chatId,$type,$file){
    if(is_file($file)){
        $file = new CURLFile($file);
    }
    $method = 'send'.$type;
    $data = [
        'chat_id'=>$chatId,
        $type=>$file
        ];
    return sendCommand($method,$data);
}
// deleteMessage function
function deleteMessage($chatId,$messageId){
    if($chatId and $messageId){
        $data = ['chat_id'=>$chat_id,'message_id'=>$messageId];
        $ret = sendCommand('deleteMessage',$data);
    }
    return $ret;
} 

// start admin functions 
    // kickChatMember function
function kickChatMember($chatId, $userId, $untilDate = null){
    $data = [
        'chat_id'=>$chatId,
        'user_id'=>$userId
        ];
    if($untilDate){
        $data['until_date'] = $untilDate;
    }
    return sendCommand('kickChatMember',$data);
}
    // promoteChatMember function
function promoteChatMember($chatId, $userId,$can_change_info = null, $can_post_messages = null, $can_edit_messages = null, $can_delete_messages = null, $can_invite_users = null, $can_restrict_members = null, $can_pin_messages = null, $can_promote_members = null){
    $data = [
		'chat_id' => $chatId,
		'user_id' => $userId
		];
	if(isset($can_change_info))
	{
		$data['can_change_info'] = $can_change_info;
	}
	if(isset($can_post_messages))
	{
		$data['can_post_messages'] = $can_post_messages;
	}
	if(isset($can_edit_messages))
	{
		$data['can_edit_messages'] = $can_edit_messages;
	}
	if(isset($can_delete_messages))
	{
		$data['can_delete_messages'] = $can_delete_messages;
	}
	if(isset($can_invite_users))
	{
		$data['can_invite_users'] = $can_invite_users;
	}
	if(isset($can_restrict_members))
	{
		$data['can_restrict_members'] = $can_restrict_members;
	}
	if(isset($can_pin_messages))
	{
		$data['can_pin_messages'] = $can_pin_messages;
	}
	if(isset($can_promote_members))
	{
		$data['can_promote_members'] = $can_promote_members;
	}
	return sendCommand('promoteChatMember',$data);
}
//function restrictChatMember
function restrictChatMember($chatId, $userId, $untilDate = null, $can_send_messages = null, $can_send_media_messages = null, $can_send_other_messages = null, $can_add_web_page_previews = null, $response = false){
    $data = [
		'chat_id' => $chatId,
		'user_id' => $userId
		];
	if(isset($untilDate))
	{
		$data['until_date'] = $untilDate;
	}
	if(isset($can_send_messages))
	{
		$data['can_send_messages'] = $can_send_messages;
	}
	if(isset($can_send_media_messages))
	{
		$data['can_send_media_messages'] = $can_send_media_messages;
	}
	if(isset($can_send_other_messages))
	{
		$data['can_send_other_messages'] = $can_send_other_messages;
	}
	if(isset($can_add_web_page_previews))
	{
		$data['can_add_web_page_previews'] = $can_add_web_page_previews;
	}
	return sendCommand('restrictChatMember',$data);
}
// unbanChatMember function
function unbanChatMember($chatId, $userId){
	$data = [
		'chat_id' => $chatId,
		'user_id' => $userId
		];
	return sendCommand('unbanChatMember',$data);
}
// pinChatMessage function 
function pinChatMessage($chatId, $messageId, $notification = null){
	$data = [
		'chat_id' => $chatId,
		'message_id' => $messageId
		];
	if(isset($notification)){
		$data['disable_notification'] = $notification;
	}
	return sendCommand('pinChatMessage',$data);
}
// unpinChatMessage function 
function unpinChatMessage($chatId){
    return sendCommand('unpinChatMessage',['chat_id'=>$chat_id]);
}
// function setChatTitle
function setChatTitle($chatId, $title)
{
	$data = [
		'chat_id' => $chatId,
		'title' => $title
		];
   return sendCommand('setChatTitle',$data); 
}
//function setChatDescription
function setChatDescription($chatId, $desc = null){
	$data = [
		'chat_id' => $chatId
		];
	if($desc != null){
		$data['description'] = $desc;
	}
	return sendCommand('setChatDescription',$data); 
}
//function setChatPhoto
function setChatPhoto($chatId, $photo){
    if(is_file($photo)){
        $photo = new CURLFile($photo);
    }
    
    $data = [
        'chat_id' => $chatId,
        'photo' => $photo
    ];
    return sendCommand('setChatPhoto', $data);
} 
// function deleteChatPhoto
function deleteChatPhoto($chatId){
    return sendCommand('deleteChatPhoto',['chat_id'=>$chatId]);
}
// getChatMembersCount function
function getChatMembersCount($chatId){
    return sendCommand('getChatMembersCount',['chat_id'=>$chatId]);
}
// getChatAdministrators function
function getChatAdministrators($chatId){
    return sendCommand('getChatAdministrators',['chat_id'=>$chatId]);
}
// getChatMember function
function getChatMember($chatId,$userId){
    return sendCommand('getChatMember',['chat_id'=>$chatId,'user_id'=>$userId]);
}



// database connect function
function databaseConnect(){
    global $databaseHost,$databaseName,$databaseUsername,$databasePassword;

    $database = new PDO("mysql:host=$databaseHost;dbname=$databaseName",$databaseUsername,$databasePassword);
    return $database;
}

// putLog function
function putLog($text){
    file_put_contents("Logs/bot.log",date("H:i:s")." => ".$text."\n",FILE_APPEND);
}

// sendDebug function
function sendDebug($data){
    if(is_array($data)){
        $data = print_r($data,true);
    }
    sendMessage("```".$data."```");
}

// sendChatAction function
function sendChatAction($arg){
    global $chatId;
    $data = [
        "chat_id" => $chatId,
        "action" => $arg
    ];
    sendCommand("sendChatAction",$data);
}

// forwardMessage function
function forwardMessage($fromChat,$fromMessage,$toChat=false){
    global $chatId;

    //if forwarded message not specified, return
    if(empty($fromChat) || empty($fromMessage)){
        return false;
    }

    $data = array(
        "from_chat_id" => $fromChat,
        "message_id" => $fromMessage
    );

    // if toChat is not specified, send message to received chat id
    if(!$toChat){
        $data["chat_id"] = $chatId;
    }else{
        $data["chat_id"] = $toChat;
    }

    $result = sendCommand("forwardMessage",$data);
    return $result;
}

// editMessage function
function editMessage($text,$chat=false,$message=false,$replyMarkup=false){
    global $chatId,$messageId;
    //if required data is not specified, return
    if(empty($text)){
        return false;
    }

    $data = array(
        "text" => $text,
        "parse_mode" => "Markdown",
    );

    //reply markup
    if($replyMarkup){
        $data["reply_markup"] = json_encode($replyMarkup);
    }

    $data["chat_id"] = $chat == false ? $chatId : $chat;
    $data["message_id"] = $message == false ? $messageId : $message;

    $result = sendCommand("editMessageText",$data);
    return $result;
}


// getChat function
function getChat($chatId){
    $result = sendCommand('getChat',['chat_id'=>$chatId]);
    return $result;
}

// getUserProfilePhotos function
function getUserProfilePhotos($userId, $offset = false, $limit = false)
{
 $data = [
   'user_id'=>$userId
  ];
 if(isset($offset))
 {
  $data['offset'] = $offset;
 }
 if(isset($limit))
 {
  $data['limit'] = $limit;
 }
    $result = sendCommand('getUserProfilePhotos', $data);
    return $result
}
