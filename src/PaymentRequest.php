<?php

namespace MoncashEasy\SDK;

class PaymentRequest {

	private $credentials;
	private $path;
	private $payment_token;
	private $timestamp;
	private $status;
	private $mode;


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

		return $this->credentials->getConfigs()['redirect_url']."".Constants::GATEWAY_URI."?token=".$this->payment_token['token'];
	}

 


	public function __toString() {
		return "PaymentRequest object: path(".$this->path.") - status(".$this->status.") - mode(".$this->mode.")";
	}



}


?>
