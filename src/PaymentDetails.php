<?php

namespace MoncashEasy\SDK;

class PaymentDetails {

	private $path;
	private $payment;
	private $timestamp;
	private $status;


	public function __construct(array $details) {
		$this->path = $details['path'];
		$this->payment = new Payment($details['payment']);
		$this->timestamp = $details['timestamp'];
		$this->status = $details['status'];

	}


	public function getPath() { return $this->path; }

	public function getPayment() { return $this->payment; }

	public function getTimestamp() { return $this->timestamp; }

	public function getStatus() { return $this->status; }



	public function __toString() {

		return "PaymentDetails object: path(".$this->getPath().") - timestamp(".$this->getTimestamp().") - status(".$this->getStatus().")";
	}



}


?>