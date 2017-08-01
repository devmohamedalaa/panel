<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class SMS extends Controller
{
    //

    public static function sms($num,$msg,$title = 'Qaf3.com')
    {
        $login = 'Jakob';
        $password = 'tQf6CJFs';
        $srcaddr = urlencode($title);
        $text = urlencode($msg);
        $num = "$num";
        $ch = curl_init();
        $data = "http://sms.jawez.com/sendsms?user=".$login."&pwd=".$password."&sadr=".$srcaddr."&text=".$text."&dadr=".$num;
        $urlData = urlencode( $data );
        curl_setopt( $ch, CURLOPT_URL, $data );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        $out = curl_exec( $ch );
        echo $out;
        curl_close( $ch );

    }
}
