<?php

namespace MoncashEasy\SDK;

class Payment {

	private $reference;
	private $transaction_id;
	private $cost;
	private $message;
	private $payer;
	


	public function __construct(array $details) {

		if(!empty($details)) {
			$this->reference = $details['reference'];
			$this->transaction_id = $details['transaction_id'];
			$this->cost = $details['cost'];
			$this->message = $details['message'];
			$this->payer = $details['payer'];
		}

	}

	public function getReference() { return $this->reference; }

	public function getTransactionId() { return $this->transaction_id; }

	public function getCost() { return $this->cost; }

	public function getMessage() { return $this->message; }

	public function getPayer() { return $this->payer; }


	public function __toString() {

		return "Payment object: reference(".$this->getReference().") - transaction_id(".$this->getTransactionId().") - cost(".$this->getCost().") - message(".$this->getMessage().") - payer(".$this->getPayer().")";

	}

}


?>
