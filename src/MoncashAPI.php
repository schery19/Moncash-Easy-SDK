<?php

namespace MoncashEasy\SDK;


class MoncashAPI {

	private $credentials;

	private $configs;

	private $token;

	private const BTN_EN = "MC_button.png";

	private const BTN_FR = "MC_button_fr.png";

	private const BTN_KR = "MC_button_kr.png";

	
	public function __construct($client_id, $client_secret, $debug = true) {

		$this->configs = Configuration::getConfigArray($debug);

		$this->credentials = new Credentials($client_id, $client_secret, $this->configs);

		$this->token = $this->getAuthInfos()['access_token'];
	}


	public function getCredentials() { return $this->credentials; }

	public function setCredentials($client_id, $client_secret) {
		$this->credentials = new Credentials($client_id, $client_secret, $this->configs);
	}
	
	
	public function getMode() { return $this->configs['mode']; }

	public function setMode(string $env) {
		if($env === Constants::SANDBOX || $env === strtoupper(Constants::SANDBOX)) {
			$this->configs = Configuration::getConfigArray(true);
		} elseif($env === Constants::LIVE || $env === strtoupper(Constants::LIVE)) {
			$this->configs = Configuration::getConfigArray(false);
		} else {
			echo new MoncashException("L'environnement doit être 'sandbox' ou 'live'");
		}
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
	 		echo new MoncashException("Impossible de s'authentifier");
	 	}

	}



	public function makePaymentRequest($order_id, $amount) {

		$url = $this->configs['api_endpoint'].Constants::PAYMENT_MAKER;
		
		if($amount <= 0)
			throw new MoncashException("Impossible d'effectuer un paiement avec un montant négatif ou nul");

		$order = array('amount'=>"$amount", 'orderId'=>"$order_id");

		$httpClient = new \Guzzle\Http\Client();

		try {

	 		$req = $httpClient->post($url, array('Accept' =>"application/json", 'authorization'=>"Bearer $this->token",'Content-type'=>"application/json"), json_encode($order));

	 		$res = $req->send();

	 		$details = json_decode($res->getBody(), true);

	 		return new PaymentRequest($this->credentials, $details);

	 	} catch(\Guzzle\Http\Exception\ClientErrorResponseException $e) {
	 		echo new MoncashException("Impossible d'effectuer le paiement");
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
	 		echo new MoncashException("Impossible d'effectuer le transfert");
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
	 		echo new MoncashException("Impossible de trouver cette commande");
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
	 		echo new MoncashException("Impossible de trouver cette commande");
	 	}

	}
	
	
	/**
	 * Génération du boutton de paiement en fonction de la 
	 * langue choisie, en cas d'absence de paramètres la
	 * version anglaise du boutton de paiement sera 
	 * générer automatiquement
	 * @param string 'FR' pour le français, 'EN' pour l'
	 * anglais et 'KR' pour le créole
	 * @return string L'url du boutton correspondant
	 */
	public function btnPay($lang = null) {

		$base_url = Constants::BASE_URL_IMG;

		$img = "";

		if($lang == "FR" || $lang == "fr") {
			$img = self::BTN_FR;
		} else if($lang == "KR" || $lang == "kr") {
			$img = self::BTN_KR;
		} else {
			$img = self::BTN_EN;
		}

		return $base_url."".$img;
	}



	public function __toString() {
		return "MoncashAPI object: Client_id (".$this->getCredentials()->getClient_id().")";
	}


}

?>
