<?php

namespace MoncashEasy\SDK;

class PaymentRequest {

	private $credentials;
	private $path;
	private $payment_token;
	private $timestamp;
	private $status;
	private $mode;
	private $redirect;


	public function __construct(Credentials $credentials, array $details) {
		$this->credentials = $credentials;
		
		$this->path = $details['path'];
		$this->payment_token = array("expired"=>$details['payment_token']['expired'], "created"=>$details['payment_token']['created'], "token"=>$details['payment_token']['token']);
		$this->timestamp = $details['timestamp'];
		$this->status = $details['status'];
		$this->mode = $details['mode'];

	}

	public function getPath() { return $this->path; }

	public function getTimestamp() { return $this->timestamp; }

	public function getStatus() { return $this->status; }

	public function getMode() { return $this->mode; }

	public function getRedirect() {

		return $this->redirect = $this->credentials->getConfigs()['redirect_url']."".Constants::GATEWAY_URI."?token=".$this->payment_token['token'];
	}


	/**
	 * Génération du boutton de paiement en fonction de la 
	 * langue choisie, en cas d'absence de paramètres la
	 * version anglaise du boutton de paiement sera 
	 * générer automatiquement
	 * @param const string Les constantes présentes dans la
	 * classe MoncashAPI
	 * @return string L'url du boutton correspondant
	 */
	public function btnPay($lang = null) {

		$base_url = Constants::BASE_URL_IMG;

		$img = "";

		if($lang === MoncashAPI::BTN_FR) {
			$img = MoncashAPI::BTN_FR;
		} else if($lang === MoncashAPI::BTN_KR) {
			$img = MoncashAPI::BTN_KR;
		} else {
			$img = MoncashAPI::BTN_EN;
		}

		return $base_url."".$img;
	} 


	public function __toString() {
		return "Payment object: path(".$this->path.") - status(".$this->status.") - mode(".$this->mode.")";
	}



}


?>