<?php
require APPPATH . 'libraries/REST_Controller.php';     

class Api extends REST_Controller {    

    public function __construct() {

		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct();
        $this->load->model("WeatherModel", "weather");
		$this->load->driver('cache', array('adapter' => 'redis','backup' => 'file'));
    }

   public function listWeatherData_get(){

		$max_calls_limit  = 5;
		$time_period      = 20;
		$total_user_calls = 0;

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$user_ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$user_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$user_ip_address = $_SERVER['REMOTE_ADDR'];
		}

		$redis = new Redis();
		$config = $this->config->item('redis');
		$host = $config['host'];    
		$port = $config['port'];
		$redis->connect($host, $port);

		if (!$redis->exists($user_ip_address)) {
			$redis->set($user_ip_address, 1);
			$redis->expire($user_ip_address, $time_period);
			$total_user_calls = 1;
			$data = $this->weather->weatherDetails();
			//$currentTime = time();
			if($this->cache->redis->is_supported() || $this->cache->file->is_supported()) 
			{
				$this->cache->save('cache_weatherdata', $data, 600);
				$weatherData = $this->cache->get('cache_weatherdata');
				$this->response($weatherData, 200);
			} else {
				$this->response('Not supporting Redis cache', 400);
			}
		} else {
			$redis->INCR($user_ip_address);
			$total_user_calls = $redis->get($user_ip_address);
			$data = $this->weather->weatherDetails();
			//$currentTime = time();
			//$cache_key = 'my_cache_key';
			
			if($this->cache->redis->is_supported() || $this->cache->file->is_supported()) 
			{
				$this->cache->save('cache_weatherdata', $data, 600);
				$weatherData = $this->cache->get('cache_weatherdata');
				$this->response($weatherData, 200);
			} else {
				$this->response('Not supporting Redis cache', 400);
			}

			if ($total_user_calls > $max_calls_limit) {
				$data = array();
				$message = "User " . $user_ip_address . " limit exceeded. You can access it in 20 seconds 5 times only";
				$data['message'] = $message;
				$this->response($data, 400);
			}
		}
   }
}

