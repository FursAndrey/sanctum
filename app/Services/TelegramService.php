<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{    
    // https://api.telegram.org/botХХХХ/deleteWebhook
    //https://telegram-bot-sdk.readme.io/reference/removewebhook
    private $bot_token; //
    
    public function __construct()
    {
        $this->bot_token = config('services.telegram.bot_token');
    } 
    
    //pdf, zip, gif
    public function sendDocument($chat_id, $document, $caption = '')
    {
        $url = 'https://api.telegram.org/bot'.$this->bot_token.'/sendDocument';
        $response = Http::post($url, [
            'chat_id' => $chat_id,
            'document' => $document,
            'caption' => $caption,
        ]);

        return $response->collect();        
    } 

    public function sendPhoto($chat_id, $photo = '', $caption = '')
    {  
        $url = 'https://api.telegram.org/bot'.$this->bot_token.'/sendPhoto';
        $response = Http::post($url, [                    
			'photo' => $photo,
			'caption' => $caption,
			'chat_id' => $chat_id,
        ]);  

        return $response->collect();      
    }

    public function sendMediaGroup($chat_id, $media, $disable_notification = true)
    {  
        $url = 'https://api.telegram.org/bot'.$this->bot_token.'/sendMediaGroup';
        $response = Http::post($url, [
			'chat_id' => $chat_id,
			'media' => $media,
			'disable_notification' => $disable_notification,
        ]);
        
        return $response->collect();      
    }

    //отправка сообщений
    public function sendMessage($chat_id, $text, $keyboard = [], $resize_keyboard = true, $one_time_keyboard = true)
    {
        $reply_markup = [
			'keyboard' => $keyboard,
			'resize_keyboard' => $resize_keyboard,
			'one_time_keyboard' => $one_time_keyboard
		]; //"one_time_keyboard" - одноразовая

        $url = 'https://api.telegram.org/bot'.$this->bot_token.'/sendmessage';
        echo $url."\n chat_id - ".$chat_id."\n text - ".$text."\n";
        $response = Http::post($url, [
            'text' => $text,
            'reply_markup' => json_encode($reply_markup),
            'chat_id' => $chat_id,
        ]);

        return $response->collect();
    }

    //отправка сообщений
    public function deleteMessage($chat_id, $message_id)
    {
        $url = 'https://api.telegram.org/bot'.$this->bot_token.'/deleteMessage';
        $response = Http::post($url, [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ]);

        return $response->collect();
    }

    //отправка сообщений
    public function setWebhook($url, $certificate = null)
    {
        $url = 'https://api.telegram.org/bot'.$this->bot_token.'/setWebhook';
        $response = Http::post($url, [
            'url'=> $url,
            'certificate' => $certificate,            
        ]);

        return $response->json();
    }
}
