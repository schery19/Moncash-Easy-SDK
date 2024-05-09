<?php 

namespace MoncashEasy\SDK;


class Configuration {



	/**
	 * Configuration des différents paramètres
	 * @param boolean $debug L'environnement d'exécution (sandbox : true | live : false)
	 * 
	 * @return array Qui contient des différentes informations 
	 * A savoir si on est en mode développement ou production
	 * Aussi les points de terminaison correspondant
	 
	*/
	
	public static function getConfigArray($debug) {

		$mode = ($debug == true)?Constants::SANDBOX:Constants::LIVE;

		$endpoint = ($debug == true)?Constants::SANDBOX_ENDPOINT:Constants::LIVE_ENDPOINT;

		$redirect = ($debug == true)?Constants::SANDBOX_REDIRECT_URL:Constants::LIVE_REDIRECT_URL;

		$ignoreSSLVerification = ($debug == true)?['verify'=>false]:['verify'=>true];


		return array("mode"=>$mode, 
					"api_endpoint"=>$endpoint, 
					"redirect_url"=>$redirect,
					"ignoreSSLVerification"=>$ignoreSSLVerification);
	}

}




?>
