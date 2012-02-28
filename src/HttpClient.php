<?php

namespace Groups;

class HttpClient{

	protected $curl;

	protected $options;

	protected $authenticated = false;

	protected $response;

	protected $error;

	protected $responseCode;

	public function __construct($options = null){

		$defaults = array('use_persistent_cookie' => true, 'api_host' => 'grou.ps', 'use_ssl' => false);

		$this->options = array_merge($defaults, (array)$options);
	}

	public function __get($prefix){

		$this->options['module'] = $prefix;

		return $this;
	}

	public function getOption($option, $default = null){

		return isset($this->options[$option]) ? $this->options[$option] : $default;
	}

	protected function initializeCurl(){

		if(!$this->curl){

			$curl = curl_init();

			if($this->getOption('use_persistent_cookie')){

				$cookie_jar = $this->getOption('cookie_jar', sys_get_temp_dir().'/grou.ps.client.cookie.txt');

				curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
				curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_jar);
			}

			if($this->getOption('debug')){

				curl_setopt($curl, CURLOPT_VERBOSE, true);
			}

			$this->curl = $curl;
		}

		return $this->curl;
	}

	public function getResponse(){

		return $this->response;
	}

	public function getError(){

		return $this->error;
	}

	public function __call($name, $arguments){

		$options = !isset($arguments[0]) ? array() : $arguments[0];

		$options['method'] = $name;
		$options['module'] = $this->getOption('module', 'generals');

		return $this->doRequest('api.php', $options);
	}

	public function doRequest($uri, $options){

		$curl = $this->initializeCurl();

		if(!isset($options['groupName'])){

			$options['groupName'] = $this->getOption('groupName');
		}

		curl_setopt($curl, CURLOPT_URL, $this->createRequestUrl($uri, $options));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response           = curl_exec($curl);
		$this->response     = $response;
		$info               = curl_getinfo($curl);
		$this->responseCode = $info['http_code'];
		$this->error 		= curl_error($curl);

		curl_close($curl);

		return $response;
	}

	protected function createRequestUrl($uri, $options){

		return sprintf('http%s://%s/%s?%s',
			$this->getOption('use_ssl') ? 's' : '',
			$this->getOption('api_host'),
			rtrim(ltrim($uri, '/'), '?'),
			http_build_query($options)
		);
	}

}