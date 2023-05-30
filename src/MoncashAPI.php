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
		$this->token = $this->getAuthInfos()['access_token'];
		
		return $this;
	}
	
	
	public function getMode() { return $this->configs['mode']; }

	public function setMode(string $env) {
		if($env === Constants::SANDBOX || $env === strtoupper(Constants::SANDBOX)) {
			$this->configs = Configuration::getConfigArray(true);
		} elseif($env === Constants::LIVE || $env === strtoupper(Constants::LIVE)) {
			$this->configs = Configuration::getConfigArray(false);
		} else {
			throw new MoncashException("L'environnement doit être 'sandbox' ou 'live'");
		}
		
		//Mode mise à jour pour toutes les classes dépendantes de Credentials
		$this->setCredentials($this->getCredentials()->getClient_id(), $this->getCredentials()->getClient_secret());
		
		return $this;
	}


	/** 
	 * Obtenir le token d'accès nécessaire aux éventuelles transactions
	 * @return array La réponse de l'API, contenant le token d'accès
	*/
	private function getAuthInfos() {

		$url_split = explode("//", $this->configs['api_endpoint']);

		$url = $url_split[0]."//".$this->getCredentials()->getClient_id().":".$this->getCredentials()->getClient_secret()."@".$url_split[1]."".Constants::OAUTH_TOKEN_URI;

		$httpClient = new \GuzzleHttp\Client(['verify'=>false]);

		try {

	 		//$req = $httpClient->post($url, array('Accept' =>"application/json"), array('scope'=>"read,write", 'grant_type'=>"client_credentials"));

			 $req = $httpClient->post($url, array(
				"form_params"=>array('scope'=>"read,write", 'grant_type'=>"client_credentials"),
				"headers"=>array('Accept' =>"application/json")
			 ));

			 $res = $req->getBody()->getContents();

	 		return json_decode($res, true);


	 	} catch(\GuzzleHttp\Exception\ClientException $e) {
	 		throw new MoncashException("Impossible de s'authentifier");
	 	}

	}


	public function makePaymentRequest($order_id, $amount) {

		$url = $this->configs['api_endpoint'].Constants::PAYMENT_MAKER;
		
		if($amount <= 0)
			throw new MoncashException("Impossible d'effectuer un paiement avec un montant négatif ou nul");

		$order = array('amount'=>"$amount", 'orderId'=>"$order_id");

		$httpClient = new \GuzzleHttp\Client(['verify'=>false]);

		try {

			 $req = $httpClient->post($url, array(
				"headers"=>array(
					'Accept' =>"application/json", 
					'authorization'=>"Bearer $this->token",
					'Content-type'=>"application/json"),
				"body"=>json_encode($order)
			 ));

			 $res = $req->getBody()->getContents();

	 		$details = json_decode($res, true);

	 		return new PaymentRequest($this->credentials, $details);

	 	} catch(\GuzzleHttp\Exception\ClientException $e) {
	 		throw new MoncashException("Impossible d'effectuer le paiement");
	 	}

	}



	public function makeTransfert($receiver, $amount, $desc) {

		$url = $this->configs['api_endpoint'].Constants::TRANSFERT;

		$transfert = array("amount"=>$amount, "receiver"=>$receiver, "desc"=>$desc);

		$httpClient = new \GuzzleHttp\Client(['verify'=>false]);

		try {

			 $req = $httpClient->post($url, array(
				"headers"=>array(
					'Accept' =>"application/json", 
					'authorization'=>"Bearer $this->token",
					'Content-type'=>"application/json"),
				"body"=>json_encode($transfert)
			 ));

	 		$res = $req->getBody()->getContents();

	 		return new Transfert(json_decode($res, true));

	 	} catch(\GuzzleHttp\Exception\ClientException $e) {
	 		throw new MoncashException("Impossible d'effectuer le transfert");
	 	}
	}


	public function getDetailsByOrderId($order_id) {

		$url = $this->credentials->getConfigs()['api_endpoint'].Constants::PAYMENT_ORDER_URI;

		$order = array("orderId"=>$order_id);

		$httpClient = new \GuzzleHttp\Client(['verify'=>false]);

		try {

			 $req = $httpClient->post($url, array(
				"headers"=>array(
					'Accept' =>"application/json", 
					'authorization'=>"Bearer $this->token",
					'Content-type'=>"application/json"),
				"body"=>json_encode($order)
			 ));
	 		
			$res = $req->getBody()->getContents();

	 		return new PaymentDetails(json_decode($res, true));


	 	} catch(\GuzzleHttp\Exception\ClientException $e) {
	 		throw new MoncashException("Impossible de trouver cette transaction");
	 	}

	}


	public function getDetailsByTransactionId($transaction_id) {

		$url = $this->credentials->getConfigs()['api_endpoint'].Constants::PAYMENT_TRANSACTION_URI;

		$transaction = array("transactionId"=>$transaction_id);

		$httpClient = new \GuzzleHttp\Client(['verify'=>false]);

		try {

	 		$req = $httpClient->post($url, array(
				"headers"=>array(
					'Accept' =>"application/json", 
					'authorization'=>"Bearer $this->token",
					'Content-type'=>"application/json"),
				"body"=>json_encode($transaction)
			 ));
			
			$res = $req->getBody()->getContents();

	 		return new PaymentDetails(json_decode($res, true));


	 	} catch(\GuzzleHttp\Exception\ClientException $e) {
	 		throw new MoncashException("Impossible de trouver cette transaction");
	 	}

	}
	
	
	/**
	 * Génération du boutton de paiement en fonction de la langue choisie, 
	 * en cas d'absence de paramètres la version anglaise du boutton de 
	 * paiement sera générer automatiquement
	 * @param string Code des trois langues disponibles : 'FR', 'EN' et 'KR'
	 * @return string L'url du boutton correspondant
	 */
	public function btnPay($lang = null) {

		$base_url = $this->configs['redirect_url'].Constants::IMG_URI;

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
