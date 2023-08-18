<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class WeatherModel extends CI_Model {

    public function weatherDetails()
	{
		$config = $this->config->item('redis');
		$apiKey = $config['api_key'];    
		$cityId = $config['city_id'];
		$googleApiUrl = "https://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($response);
		return $data;
    }
}

