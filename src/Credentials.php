<?php

namespace MoncashEasy\SDK;

class Credentials {

	private $client_id;
	private $client_secret;
	private $configs;
	private $access_token;


	public function __construct($client_id, $client_secret, array $configs) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->configs = $configs;
	}


	public function getClient_id() { return $this->client_id; }

	public function getClient_secret() { return $this->client_secret; }

	public function getConfigs() { return $this->configs; }
	
}




?>