<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;

class TestController extends Controller
{

    public function store()
    {

    	$image = Image::make(public_path('img/พื้นหลัง/พื้นหลัง-05.png'));

	    $watermark_2 = Image::make( public_path('img/logo/green-logo-01.png') );
	    $image->insert($watermark_2 ,'bottom-right', 385, 150);

        $filename = $this->random_string(10).".png";
        $image->save( public_path('img/พื้นหลัง/' . $filename) );

        return view('welcome');
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
