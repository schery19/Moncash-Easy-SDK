<?php

namespace MoncashEasy\SDK;

class Transfert {

	private $path;
	private $transfertDetails;
	private $timestamp;
	private $status;


	public function __construct(array $details) {
		
		$this->path = $details['path'];
		$this->transfertDetails = array("transaction_id"=>$details['transfer']['transaction_id'], "amount"=>$details['transfer']['amount'], "receiver"=>$details['transfer']['receiver'], "message"=>$details['transfer']['message'], "description"=>$details['transfer']['desc']);
		$this->timestamp = $details['timestamp'];
		$this->status = $details['status'];
	}


	public function getPath() { return $this->path; }

	public function getTransfertDetails() { return $this->transfertDetails; }

	public function getTimestamp() { return $this->timestamp; }

	public function getStatus() { return $this->status; }


	public function __toString() {
		return "Transfert object: transaction_id(".$this->transfertDetails['transaction_id'].") - amount(".$this->transfertDetails['amount'].") - receiver(".$this->transfertDetails['receiver'].") - message(".$this->transfertDetails['message'].") - description(".$this->transfertDetails['description'].")";
	}
	
}

?>
