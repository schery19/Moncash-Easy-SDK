<?php

namespace MoncashEasy\SDK;

use \Exception as Exception;
use \GuzzleHttp\Exception\ClientException as GuzzleException;


class MoncashException extends Exception {
	
	public function __construct($e) {
        
		if($e instanceof GuzzleException) {

		    $response = $e->getResponse();

		    $this->code = $response->getStatusCode();

		    switch($response->getStatusCode()) {
			case 401 :
			    $this->message = "Impossible de s'authentifier";
			    break;
			case 404 :
			    $this->message = "Ressource introuvable";
			    break;
			default :
			    $this->message = $e->getMessage();
			    break;
		    }

		} else {
		    $this->message = $e;
		} 
   	 }
}

?>
