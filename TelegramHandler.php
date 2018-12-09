<?php
use \unreal4u\TelegramAPI\HttpClientRequestHandler;
use \unreal4u\TelegramAPI\TgLog;
use \unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
include('vendor/autoload.php');

class TelegramHandler {

  public function sendTelegramMessage($textMessage){

    $loop = \React\EventLoop\Factory::create();
    $handler = new HttpClientRequestHandler($loop);
    
    //INSERT YOUR BOT API HERE
    $tgLog = new TgLog("", $handler);


    $sendMessage = new SendMessage();
    //INSERT YOUR CHAT ID HERE
    $sendMessage->chat_id = 0;
    $sendMessage->text = $textMessage;

    $tgLog->performApiRequest($sendMessage);
    $loop->run();
  }

}

?>