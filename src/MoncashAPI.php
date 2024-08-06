<?php

namespace MoncashEasy\SDK;


class MoncashAPI {

	private $credentials;

	private $configs;

	private $token;

	private const BTN_EN = "MC_button.png";

	private const BTN_FR = "MC_button_fr.png";

	private const BTN_KR = "MC_button_kr.png";


	/**
	 * Instance de MoncashAPI à partir de laquelle on pourra effectuer les diverses opérations
	 * 
	 * @param string $client_id Votre client id
	 * @param string $client_secret Votre client secret
	 * @param bool $debug L'environnement d'exécution (sandbox : true | live : false), valeur par défaut : true
	 * 
	 * @throws MoncashException
	 */
	public function __construct($client_id, $client_secret, $debug = true) {

		$this->configs = Configuration::getConfigArray($debug);

		$this->credentials = new Credentials($client_id, $client_secret, $this->configs);

		try {
			$this->token = $this->getAuthInfos()['access_token'];
		}catch(\Exception $e) {
			throw new MoncashException($e);
		}
	}


	/**
	 * Les identifications du compte Moncash business et les configurations
	 * 
	 * @return Credentials
	 */
	public function getCredentials() { return $this->credentials; }


	/**
	 * Modifier les informations du compte Moncash business, et obtenir éventuellement un nouveau token
	 * 
	 * @param string $client_id Votre nouveau client id
	 * @param string $client_secret Votre nouveau client secret
	 * 
	 * @return MoncashAPI
	 * 
	 * @throws MoncashException
	 */
	public function setCredentials($client_id, $client_secret) {
		$this->credentials = new Credentials($client_id, $client_secret, $this->configs);

		try {
			$this->token = $this->getAuthInfos()['access_token'];
		}catch(\Exception $e) {
			throw new MoncashException($e);
		}
		
		return $this;
	}
	
	
	/**
	 * L'environnement d'exécution (sandbox : true | live : false)'
	 * 
	 * @return string (sandbox | live)
	 */
	public function getMode() { return $this->configs['mode']; }


	/**
	 * Changer l'environnement d'exécution
	 * 
	 * @param string $env Votre nouvel environnement (live | sandbox)
	 * 
	 * @return MoncashAPI
	 * 
	 * @throws MoncashException
	 */
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
	 * Obtenir les informations d'authentification
	 * Principalement le token d'accès nécessaire aux éventuelles transactions
	 * 
	 * @return array La réponse de l'API, contenant le token d'accès
	*/
	private function getAuthInfos() {

		$url_split = explode("//", $this->configs['api_endpoint']);

		$url = $url_split[0]."//".$this->getCredentials()->getClient_id().":".$this->getCredentials()->getClient_secret()."@".$url_split[1]."".Constants::OAUTH_TOKEN_URI;

		try {

			$headers = array('Accept' => 'application/json');

			$data = array('scope'=>"read,write", 'grant_type'=>"client_credentials");
			

			$res = RequestHandler::execute($url, 'POST', $headers, $data, $this->configs['mode']);

			if($res['code'] >= 400) 
				throw new MoncashException($res);
			

	 		return json_decode($res['response'], true);


	 	} catch(MoncashException $e) {
	 		throw new MoncashException($e);
	 	}

	}


	/**
	 * Effectuer une requête de paiement
	 * Avec laquelle vous allez obtenir le lien de redirection pour confirmer le paiement
	 * 
	 * @param mixed $order_id Une identification unique, par exemple l'id d'un panier
	 * @param float $amount	Le montant attendu
	 * 
	 * @return PaymentRequest Utilisez la méthode <strong>getRedirect()</strong> sur la valeur retournée pour obtenir le lien de redirection
	 * 
	 * @throws MoncashException
	 */
	public function makePaymentRequest($order_id, $amount) {

		$url = $this->configs['api_endpoint'].Constants::PAYMENT_MAKER;
		
		if($amount <= 0)
			throw new MoncashException("Impossible d'effectuer un paiement avec un montant négatif ou nul");

		$order = array('amount'=>"$amount", 'orderId'=>"$order_id");

		try {

			$headers = array(
				'Accept' =>"application/json", 
				'Authorization'=>"Bearer $this->token",
				'Content-Type'=>"application/json");

			$res = RequestHandler::execute($url, 'POST', $headers, $order, $this->configs['mode']);

			if($res['code'] >= 400) 
				throw new MoncashException($res);

	 		$details = json_decode($res['response'], true);

	 		return new PaymentRequest($this->credentials, $details);

	 	} catch(MoncashException $e) {
	 		throw new MoncashException($e);
	 	}

	}


	/**
	 * Effectuer une requête de transfert
	 * 
	 * @param mixed $receiver Le numéro bénéficiaire du transfert
	 * @param float $amount Le montant du transfert
	 * @param string $desc Une description sur le transfert
	 * 
	 * @return Transfert
	 * 
	 * @throws MoncashException
	 */
	public function makeTransfert($receiver, $amount, $desc) {

		$url = $this->configs['api_endpoint'].Constants::TRANSFERT;

		$transfert = array("amount"=>$amount, "receiver"=>$receiver, "desc"=>$desc);

		try {

			$headers = array(
				'Accept' =>"application/json", 
				'Authorization'=>"Bearer $this->token",
				'Content-Type'=>"application/json");
			
			$res = RequestHandler::execute($url, 'POST', $headers, $transfert, $this->configs['mode']);

			if($res['code'] >= 400) 
				throw new MoncashException($res);

	 		return new Transfert(json_decode($res['response'], true));

	 	} catch(MoncashException $e) {
	 		throw new MoncashException($e);
	 	}
	}


	/**
	 * Obtenir les détails du paiement à partir de son identification unique
	 * 
	 * @param mixed $order_id L'identification unqiue
	 * 
	 * @return PaymentDetails
	 * 
	 * @throws MoncashException
	 */
	public function getDetailsByOrderId($order_id) {

		$url = $this->credentials->getConfigs()['api_endpoint'].Constants::PAYMENT_ORDER_URI;

		$order = array("orderId"=>$order_id);

		try {

			$headers = array(
				'Accept' =>"application/json", 
				'Authorization'=>"Bearer $this->token",
				'Content-Type'=>"application/json");

			$res = RequestHandler::execute($url, 'POST', $headers, $order, $this->configs['mode']);

	 		return new PaymentDetails(json_decode($res['response'], true));

	 	} catch(MoncashException $e) {
	 		throw new MoncashException($e);
	 	}

	}


	/**
	 * Obtenir les détails du paiement à partir du numéro de transaction fournit par l'api moncash
	 * 
	 * @param mixed $transaction_id L'identification unqiue
	 * 
	 * @return PaymentDetails
	 * 
	 * @throws MoncashException
	 */
	public function getDetailsByTransactionId($transaction_id) {

		$url = $this->credentials->getConfigs()['api_endpoint'].Constants::PAYMENT_TRANSACTION_URI;

		$transaction = array("transactionId"=>$transaction_id);

		try {

			$headers = array(
				'Accept' =>"application/json", 
				'Authorization'=>"Bearer $this->token",
				'Content-Type'=>"application/json");

			$res = RequestHandler::execute($url, 'POST', $headers, $transaction, $this->configs['mode']);

	 		return new PaymentDetails(json_decode($res['response'], true));


	 	} catch(MoncashException $e) {
	 		throw new MoncashException($e);
	 	}

	}
	
	
	/**
	 * Génération du boutton de paiement en fonction de la langue choisie
	 * En cas d'absence de paramètres la version anglaise du boutton de paiement sera générée automatiquement
	 * 
	 * @param string Code des trois langues disponibles : ('FR' | 'EN' | 'KR')
	 * 
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
