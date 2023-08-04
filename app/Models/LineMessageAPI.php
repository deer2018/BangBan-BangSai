<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LineMessageAPI extends Model
{
    use HasFactory;


    public  function replyUser($requestData)
    {
        // for verify only
        if (count($requestData["events"]) == 0)  return;
        $event = $requestData["events"][0];

        switch ($event["message"]["type"]) {
            case "text":
                $text = $event["message"]["text"];
                if (str_contains($text, "ปัจจุบัน")) {
                    $this->replyWithText($event, "นี่คือข้อมูลระดับน้ำปัจจุบัน");
                } else if (str_contains($text, "คาดการณ์")) {
                    $this->replyWithText($event, "นี่คือข้อมูลคาดการณ์");
                } else if (str_contains($text, "ภาพถ่าย") || str_contains($text, "ถ่ายภาพ")) {
                    $this->replyWithText($event, "วิเคราะห์ข้อมูลจากภาพถ่าย");
                } else {
                    $this->replyWithText($event, "Hello World");
                }
                break;
            case "location":
                $this->replyWithFlexCarousel($event);
                break;
            case "sticker":
                $this->replyWithFlexBubble($event);
                break;
            case "image":
                // $this->imageHandler($event);
                break;
        }
    }

    public  function replyWithFlexCarousel($event)
    {
        $template_path = storage_path('../public/flex-templates/places.json');
        $string_json = file_get_contents($template_path);
        $message = [
            "type" => "flex",
            "altText" => "10 สถานที่แนะนำที่ใกล้คุณ",
            "contents" => json_decode($string_json, true),
        ];

        $this->reply($event, $message);
    }
    public  function replyWithFlexBubble($event)
    {
        $template_path = storage_path('../public/flex-templates/bubble.json');
        $string_json = file_get_contents($template_path);
        $message = [
            "type" => "flex",
            "altText" => "สถานที่ใกล้คุณ",
            "contents" => json_decode($string_json, true),
        ];

        $this->reply($event, $message);
    }

    // public  function replyImage($event, $image)
    // {
    //     $message = [
    //         "type" => "text",
    //         "text" => $image,
    //     ];        
    //     $this->reply($event, $message);
    // }

    public  function replyWithText($event, $text)
    {
        $message = [
            "type" => "text",
            "text" => $text,
        ];
        $this->reply($event, $message);
    }

    public  function reply($event, $message)
    {
        //GET ONLY FIRST EVENT
        $replyToken = $event["replyToken"];

        // echo $event;
        // $channel_access_token = $this->channel_access_token;
        // $event['message'] = ['id' => ''.$data['msgocrid'] ];
        $body = [
            "replyToken" => $replyToken,
            "messages" => [$message],
        ];

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json \r\n" .
                    'Authorization: Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN'),
                'content' => json_encode($body, JSON_UNESCAPED_UNICODE),
                //'timeout' => 60
            ]
        ];

        $context  = stream_context_create($opts);
        //https://api-data.line.me/v2/bot/message/11914912908139/content
        $url = "https://api.line.me/v2/bot/message/reply";
        $result = file_get_contents($url, false, $context);
        file_put_contents('../storage/logs/log.txt', $result . PHP_EOL, FILE_APPEND);
    }
}
