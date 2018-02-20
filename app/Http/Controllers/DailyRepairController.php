<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Curl;
use Ixudra\Curl\Facades\Curl;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class DailyRepairController extends Controller
{
    public function index(Request $request){
    	//make CURL http://localhost/fa_quality/public/api/qualities
    	
    	$url = 'http://localhost/fa_quality/public/api/qualities';
        $hostname = apache_request_headers();
        $hostname = $hostname['Host'];
        
    	$response = Curl::to($url)
    	->enableDebug('./logFile.txt')
        ->withHeader('Accept: application/json')
    	// ->withProxy('136.198.117.21', 8080)
        ->withProxy( $hostname , 80)
        // ->asJson()
        ->get();        

        // sleep(1);
        // return $url;
        return  $response;
        
    }

    public function index222(Request $request){
        $client = new \GuzzleHttp\Client([
            'timeout' => 10.0,
            'cookie' => true,
            'proxy' => [
                'http'=>'http://136.198.117.21:8080',
                'no' =>'localhost'
            ]
        ]);

        // $url = 'http://localhost/fa_quality/public/api/qualities';
        $url = 'https://jsonplaceholder.typicode.com/posts';
        $res = $client->request('GET', $url );
        echo $res->getStatusCode();
        // 200
        echo $res->getHeaderLine('content-type');
        // 'application/json; charset=utf8'
        echo $res->getBody();
        // '{"id": 1420053, "name": "guzzle", ...}'
    }


    public function store(Request $request){
    	
    }

    public function destroy(Request $request){
    	
    }

    public function update(Request $request){
    	
    }
}
