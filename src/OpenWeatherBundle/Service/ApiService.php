<?php
namespace OpenWeatherBundle\Service;

class ApiService
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

    public function open_weather($name)
    {
    	$result = json_encode([]);
        if (!empty($name)) {
	    	$url = "http://api.openweathermap.org/data/2.5/weather?q=".$name.",au&appid=e0fcfb15632a36e208c687d787e76d27";
	    	$result = self::getData($url);
        }

        return $result;
    }

}