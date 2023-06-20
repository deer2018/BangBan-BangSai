<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Mylog;

class TestController extends Controller
{

    public function store()
    {

    	$text_json = '{"destination":"Ubf0ea4dab738442af3d0d9092c62ac5f","events":[{"type":"message","message":{"type":"image","id":"458666893059293493","contentProvider":{"type":"line"}},"webhookEventId":"01H2D6VWJFHAXZE3VSAQZDFGFB","deliveryContext":{"isRedelivery":false},"timestamp":1686218076739,"source":{"type":"user","userId":"Ua561f9244840375d1d97d7550d22fb68"},"replyToken":"b93c5b87e0dd42bfa10e15ab5631429f","mode":"active"}]}';

    	$sss = json_decode($text_json, true);
    	$event = $sss["events"][0];

    	// dd($event);
    	// echo "<pre>";
    	// print_r($event);
    	// echo "<pre>";

    	//LOAD REMOTE IMAGE AND SAVE TO LOCAL
        $binary_data  = $this->getImageFromLine($event["message"]["id"]);
        $filename = $this->random_string(20).".png";
        $new_path = storage_path('app/public').'/uploads/ocr/'.$filename;

        $image_data = base64_encode(file_get_contents($new_path));

        Image::make($binary_data)->save($new_path);

    	// $image = Image::make(public_path('img/พื้นหลัง/พื้นหลัง-05.png'));

	    // $watermark_2 = Image::make( public_path('img/logo/green-logo-01.png') );
	    // $image->insert($watermark_2 ,'bottom-right', 385, 150);

     //    $filename = $this->random_string(10).".png";
     //    $image->save( public_path('img/พื้นหลัง/' . $filename) );



        return view('welcome');
    }

    public function image_convert()
    {
        $text_json = '{
            "destination": "Ubf0ea4dab738442af3d0d9092c62ac5f",
            "events": [
                {
                    "type": "message",
                    "message": {
                        "type": "text",
                        "id": "459507626582475378",
                        "text": "……."
                    },
                    "webhookEventId": "01H2W4RQYCFZKXTVFNTZJY754K",
                    "deliveryContext": {
                        "isRedelivery": false
                    },
                    "timestamp": 1686719192588,
                    "source": {
                        "type": "user",
                        "userId": "Ua561f9244840375d1d97d7550d22fb68"
                    },
                    "replyToken": "d3504663c20d482d9dc40796442e38e4",
                    "mode": "active"
                }
            ]
        }';

    	$sss = json_decode($text_json, true);
    	$event = $sss["events"][0];
        // //LOAD REMOTE IMAGE AND SAVE TO LOCAL
        // $binary_data  = $this->getImageFromLine($event["message"]["id"]);

        $img = Image::make('https://img.freepik.com/free-vector/hand-painted-watercolor-pastel-sky-background_23-2148902771.jpg');
        $img->resize(1080 , 1920);
        $filename = $this->random_string(20).".png";
        $new_path = storage_path('app/public').'/uploads/ocr/'.$filename;

        Image::make($img)->save($new_path);

        $image = Image::make( storage_path('app/public').'/uploads/ocr/'.$filename );
        $watermark = Image::make( public_path('img/logo/green-logo-01.png') );
        $image->insert($watermark ,'bottom-right', 385, 150);
        $image->save();

        // Convert image data to base64
        // $image_data = base64_encode(file_get_contents($new_path));

        $messages = [
            [
                'type' => 'image',
                'originalContentUrl' => 'https://www.mithcare.com/'.$image, // Replace with the URL of the image to send
                'previewImageUrl' => 'https://www.mithcare.com/'.$image, // Replace with the URL of a preview image
            ]
        ];

        $body = [
            "replyToken" => $event["replyToken"],
            "messages" => $messages,
        ];

        $content = json_encode($body);

        $opts = [
            'http' =>[
                'method'  => 'POST',
                'header'  => "Content-Type: application/json \r\n".
                            'Authorization: Bearer '.env('CHANNEL_ACCESS_TOKEN'),
                'content' => $content,
            ]
        ];

        $context  = stream_context_create($opts);
        $url = "https://api.line.me/v2/bot/message/reply";
        $result = file_get_contents($url, false, $context);

        //SAVE LOG
        $data = [
            "title" => "ตอบกลับผู้ใช้",
            "content" => "สวัสดีค่ะ ได้รับรูปภาพแล้วค่ะ",
        ];
        MyLog::create($data);
        return $result;
    }


    public function getImageFromLine($id){
        $opts = array('http' =>[
                'method'  => 'GET',
                //'header'  => "Content-Type: text/xml\r\n".
                'header' => 'Authorization: Bearer '.env('CHANNEL_ACCESS_TOKEN'),
                //'content' => $body,
                //'timeout' => 60
            ]
        );

        $context  = stream_context_create($opts);
        //https://api-data.line.me/v2/bot/message/11914912908139/content
        $url = "https://api-data.line.me/v2/bot/message/{$id}/content";
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    public function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }
}
