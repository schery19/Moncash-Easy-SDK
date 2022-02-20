# Moncash-Easy-SDK
Une librairie PHP permettant d'utiliser les services Moncash dans un projet, basée sur la version 1 de la [documentation](https://sandbox.moncashbutton.digicelgroup.com/Moncash-business/resources/doc/RestAPI_MonCash_doc.pdf) officielle de l'API REST de Moncash, cette librairie vise à offrir une interface de communication la plus facile possible avec l'API REST de Moncash.


Installation
-----

Utilisez [composer](https://getcomposer.org/download/) pour installer MoncashEasy, après avoir modifié son chemin [global](https://askcodez.com/modifier-le-chemin-global-du-composeur-windows.html), exécutez la commande suivante:

```php
composer require moncasheasy/moncash-easy-sdk
```


Prérequis
-----

Pour utiliser MoncashEasy il faut d'abord avoir un compte marchand sur le site de [MonCash](https://sandbox.moncashbutton.digicelgroup.com/Moncash-business/New), ce compte vous servira pour tester votre intégration.

Après avoir entrer les informations nécessaires, vous serez diriger vers une nouvelle page sur laquelle vous obtiendrez votre clientID et clientSecret qui seront très utiles pour l'utilisation de MoncashEasy.


Utilisations
-----

Dans un fichier dans le repertoire source (src/) de votre projet :

```php
require '../vendor/autoload.php';

use MoncashEasy\SDK\MoncashAPI;

$clientId = "777fab7666d7b7132ed6dd686d5f3723";
$clientSecret = "oHrr4tbnB1PH0uz6VQNUvSD5w1LbDsJavGEHpJkHoQ7yhcrTcsY2Hu8TcI7lEwcE";

/**
 * Instantiation de l'objet MoncashAPI avec comme arguments :
 * $clientId et $clientSecret qui sont à récupérer sur le 
 * site moncash après avoir créé votre compte business
 * un troisième argument ($debug) pour spécifier 
 * l'environnement, par défaut (true) il est facultatif 
 * passez le à false en mode production.
*/
$myAPI = new MoncashAPI($clientId, $clientSecret);

//L'utilisateur arrive pour la 1ère fois sur la page
if(!isset($_GET['paid'])) {

	//Effectuer un paiement

	$orderId = 93;//Un identification unique pour le paiement
	$amount = 120;//Le montant du paiement

	$payReq = $myAPI->makePaymentRequest($orderId, $amount);

	/**
	 * Permet à l'utilisateur de se rendre sur le site de Moncash
	 * pour finaliser le paiement, en lui proposant un boutton qui
	 * peut-être afficher en fonction de la langue désirée
	*/

	?>

	<p><a href='<?php echo $payReq->getRedirect(); ?>'><img src='<?php echo $payReq->btnPay(); ?>' width="120px" height="50px"></a></p>
	<?php

} else {//L'utilisateur vient d'être redirigé sur la page

	//Afficher les détails sur le paiement, qui vient d'être 
	//finalisé, avec l'objet PaymentDetails
	$payDetails = $myAPI->getDetailsByOrderId(93);

	echo "Date de la transaction : ".Date("d/m/Y", $payDetails->getTimestamp()/1000)."<br/>";

	echo "Reference : ".$payDetails->getPayment()->getReference();
	echo "<br/>";
	echo "No Transaction : ".$payDetails->getPayment()->getTransactionId();
	echo "<br/>";
	echo "Prix : ".$payDetails->getPayment()->getCost();
	echo "<br/>";
	echo "Message : ".$payDetails->getPayment()->getMessage();
	echo "<br/>";
	echo "Numéro tél : ".$payDetails->getPayment()->getPayer();
	echo "<br/>";
}
```
<strong>Notes :</strong>
<ul>
	<li>Vous utilisez la méthode btnPay() sur l'objet PaymentRequest en lui passant comme argument, des constantes comme, MoncashAPI::BTN_FR ou MoncashAPI::BTN_KR pour afficher le button respectivement en francais ou en créole, la valeur par défaut est MoncashAPI::BTN_EN qui affiche la version anglaise</li>
	<li>Vous pouvez aussi récupérer les détails du paiement avec la méthode getDetailsByTransactionId sur l'objet PaymentRequest</li>
	<li>Le paramètre 'paid' est spécifié dans l'url de redirection lors de la création du compte business, par exemple en mode développement on a spécifié : 'localhost:80/monProjet/pay.php?paid=1'</li>
</ul>


Extras
-----

Pour toutes suggestions contacter au schneiderchery7@gmail.com

Toutes améliorations et ajouts de fonctionnalités sont les bienvenues.
