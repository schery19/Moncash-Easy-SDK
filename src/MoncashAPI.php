<?php

namespace MoncashEasy\SDK;


class MoncashAPI {

	private $credentials;

	private $configs;

	private $token;

	const BTN_EN = "MC_button.png";

	const BTN_FR = "MC_button_fr.png";

	const BTN_KR = "MC_button_kr.png";

	
	public function __construct($client_id, $client_secret, $debug = true) {

		$this->configs = Configuration::getConfigArray($debug);

		$this->credentials = new Credentials($client_id, $client_secret, $this->configs);

		$this->token = $this->getAuthInfos()['access_token'];
	}


	public function getCredentials() { return $this->credentials; }

	public function setCredentials($client_id, $client_secret) {
		$this->credentials = new Credentials($client_id, $client_secret, $this->configs);
	}


	/** 
	 * Obtenir le token d'accès nécessaire aux éventuelles 
	 * transactions
	 * @return array La réponse de l'API, contenant le token
	 * d'accès
	*/
	private function getAuthInfos() {

		$url_split = explode("//", $this->configs['api_endpoint']);

		$url = $url_split[0]."//".$this->getCredentials()->getClient_id().":".$this->getCredentials()->getClient_secret()."@".$url_split[1]."".Constants::OAUTH_TOKEN_URI;

		$httpClient = new \Guzzle\Http\Client();

		try {

	 		$req = $httpClient->post($url, array('Accept' =>"application/json"), array('scope'=>"read,write", 'grant_type'=>"client_credentials"));

	 		$res = $req->send();

	 		return json_decode($res->getBody(), true);


	 	} catch(\Guzzle\Http\Exception\ClientErrorResponseException $e) {
	 		echo $e->getMessage();
	 	}

	}



	public function makePaymentRequest($order_id, $amount) {

		$url = $this->configs['api_endpoint'].Constants::PAYMENT_MAKER;

		$order = array('amount'=>"$amount", 'orderId'=>"$order_id");

		$httpClient = new \Guzzle\Http\Client();

		try {

	 		$req = $httpClient->post($url, array('Accept' =>"application/json", 'authorization'=>"Bearer $this->token",'Content-type'=>"application/json"), json_encode($order));

	 		$res = $req->send();

	 		$details = json_decode($res->getBody(), true);

	 		return new PaymentRequest($this->credentials, $details);

	 	} catch(\Guzzle\Http\Exception\ClientErrorResponseException $e) {
	 		echo $e->getMessage();
	 	}

	}



	public function makeTransfert($receiver, $amount, $desc) {

		$url = $this->configs['api_endpoint'].Constants::TRANSFERT;

		$transfert = array("amount"=>$amount, "receiver"=>$receiver, "desc"=>$desc);

		$httpClient = new \Guzzle\Http\Client();

		try {

	 		$req = $httpClient->post($url, array('Accept' =>"application/json", 'authorization'=>"Bearer $this->token",'Content-type'=>"application/json"), json_encode($transfert));

	 		$res = $req->send();

	 		return new Transfert(json_decode($res->getBody(), true));

	 	} catch(\Guzzle\Http\Exception\ClientErrorResponseException $e) {
	 		echo $e->getMessage();
	 	}
	}


	public function getDetailsByOrderId($order_id) {

		$url = $this->credentials->getConfigs()['api_endpoint'].Constants::PAYMENT_ORDER_URI;


		$order = array("orderId"=>$order_id);

		$httpClient = new \Guzzle\Http\Client();

		try {

	 		$req = $httpClient->post($url, array('Accept' =>"application/json", 'authorization'=>"Bearer $this->token",'Content-type'=>"application/json"), json_encode($order));

	 		$res = $req->send();

	 		return new PaymentDetails(json_decode($res->getBody(), true));


	 	} catch(\Guzzle\Http\Exception\ClientErrorResponseException $e) {
	 		echo $e->getMessage();
	 	}

	}


	public function getDetailsByTransactionId($transaction_id) {

		$url = $this->credentials->getConfigs()['api_endpoint'].Constants::PAYMENT_TRANSACTION_URI;

		$transaction = array("transactionId"=>$order_id);

		$httpClient = new \Guzzle\Http\Client();

		try {

	 		$req = $httpClient->post($url, array('Accept' =>"application/json", 'authorization'=>"Bearer $this->token",'Content-type'=>"application/json"), json_encode($transaction));

	 		$res = $req->send();

	 		return new PaymentDetails(json_decode($res->getBody(), true));


	 	} catch(\Guzzle\Http\Exception\ClientErrorResponseException $e) {
	 		echo $e->getMessage();
	 	}

	}



	public function __toString() {
		return "MoncashAPI object: Client_id (".$this->getCredentials()->getClient_id().")";
	}


}

?>
