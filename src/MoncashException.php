<?php

namespace MoncashEasy\SDK;

use \Exception as Exception;


class MoncashException extends Exception {
	
	public function __construct($e) {

		if(is_array($e)) {

			switch($e['code']) {
				case 401 :
					$this->message = "Impossible de s'authentifier";
					break;
				case 404 :
					$this->message = "Ressource introuvable";
					break;
				case 405 :
					$this->message = "Méthode non autorisée";
					break;
				default :
					$this->message = $e['response'];
					break;
			}

		} else {
			$this->message = $e;
		}

   	 }
}

?>
