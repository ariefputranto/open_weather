<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ApiController
{
	private function getData($url)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_USERAGENT => 'desktop'
		));
		$resp = curl_exec($curl);
		curl_close($curl);
		return $resp;
	}

    public function open_weather($location)
    {
    	$result = json_encode([]);
        if (!empty($location)) {
	    	$url = "http://api.openweathermap.org/data/2.5/weather?q=".$location."&appid=e0fcfb15632a36e208c687d787e76d27";
	    	$result = self::getData($url);
        }

        return $result;
    }

    public function getCity($city_name)
    {
    	$result = json_encode([]);
        if (!empty($location)) {
	    	$url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=".$city_name."&types=(cities)&key=AIzaSyBmqe7AJP83sQpPrXOnxiG4zWiHqrgGTVs";
	    	$result = self::getData($url);
        }

        return $result;
    }

}