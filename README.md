# Moncash-Easy-SDK
Une librairie PHP permettant d'utiliser les services Moncash dans un projet, basée sur la version 1 de la [documentation](https://sandbox.moncashbutton.digicelgroup.com/Moncash-business/resources/doc/RestAPI_MonCash_doc.pdf) officielle de l'API REST de Moncash, cette librairie vise à offrir une interface de communication la plus facile possible avec l'API REST de Moncash.


Installation
-----

Dans la racine du dossier de votre projet, créez un nouveau fichier <b>composer.json</b> avec le contenu suivant :

```php
{
    "require": {
        "moncasheasy/moncash-easy-sdk": "^1.5"
    }
}
```

Utilisez [composer](https://getcomposer.org/download/) pour installer MoncashEasy et ses dépendances, après avoir modifié son chemin [global](https://askcodez.com/modifier-le-chemin-global-du-composeur-windows.html), exécutez la commande suivante sur votre terminal en vous positionnant dans le dossier de votre projet :

```bash
composer install
```

Vous pouvez aussi tapez directement la commande suivante :

```bash
composer require moncasheasy/moncash-easy-sdk
```

Si vous ignorez quelle version installer, dans ce cas la version la plus récente sera installée


Prérequis
-----

Pour utiliser MoncashEasy il faut d'abord avoir un compte marchand sur le site de [MonCash](https://sandbox.moncashbutton.digicelgroup.com/Moncash-business/New), ce compte vous servira pour tester votre intégration.

Après avoir entré les informations nécessaires, vous serez dirigé vers une nouvelle page sur laquelle vous obtiendrez votre clientID et clientSecret qui seront très utiles pour l'utilisation de MoncashEasy.


Utilisations
-----

Dans un fichier dans le repertoire source (src/) de votre projet :
Instantiez l'objet MoncashAPI avec comme arguments : `$clientId` et `$clientSecret` qui sont à récupérer sur le site moncash après avoir créé votre compte business, un troisième argument `$debug` spécifie l'environnement, par défaut il est à `true`, passez le à `false` en mode production.

```php
require '../vendor/autoload.php';

use MoncashEasy\SDK\MoncashAPI;

$clientId = "<votre client id>";
$clientSecret = "<votre client secret>";

$moncash = new MoncashAPI($clientId, $clientSecret);

```

Pour effectuer un paiement vous utilisez l'objet PaymentRequest, qui vous donnera par la suite un moyen d'obtenir le lien qui dirigera l'utilisateur sur le site moncash pour finaliser le processus de paiement :

```php
<?php
//Effectuer un paiement

$orderId = 93;//Une identification unique pour le paiement
$amount = 120;//Le montant du paiement

$payReq = $moncash->makePaymentRequest($orderId, $amount);

?>

<p><a href='<?= $payReq->getRedirect(); ?>'><img src='<?= $moncash->btnPay(); ?>' width="120px" height="50px"></a></p>

```
Utlisez la méthode `btnPay('fr')` ou `btnPay('kr')` sur l'objet MoncashAPI pour afficher le boutton moncash respectivement en français ou en créole, sans argument cette méthode affiche la version anglaise du boutton.


Après finalisation du processus de paiement, vous pouvez récupérer les informations à partir de l'objet PaymentDetails

```php
$payDetails = $moncash->getDetailsByOrderId(93);

echo "Date de la transaction : ".Date("d/m/Y", $payDetails->getTimestamp()/1000)."<br/>";

echo "Reference : ".$payDetails->getPayment()->getReference()."<br/>";
echo "No Transaction : ".$payDetails->getPayment()->getTransactionId()."<br/>";
echo "Prix : ".$payDetails->getPayment()->getCost()."<br/>";
echo "Message : ".$payDetails->getPayment()->getMessage()."<br/>";
echo "Numéro tél : ".$payDetails->getPayment()->getPayer()."<br/>";

```


<strong>Notes :</strong>
Vous pouvez aussi récupérer les détails du paiement avec la méthode `getDetailsByTransactionId($transaction_id)` sur l'objet MoncashAPI



Certaines opérations sont succeptibles de déclencher des exceptions, surtout en cas d'erreur au niveau des données fournies, avec MoncashEasy il est possible de capturer ces exceptions :

```php
try {

	$moncash = new MoncashAPI($id, $secret);

	//Les opérations qui s'en suivent

} catch(MoncashEasy\SDK\MoncashException $e) {
	echo "Erreur : ".$e->getMessage();
}

```


Si vous avez besoin de changer de compte, pas besoin de réinstantier l'objet `$moncash`, vous n'avez qu'à utiliser la méthode `setCredentials($id, $secret)` pour assigner à l'objet les nouvels identifiants correspondant au nouveau compte

```php
try {

	$moncash = new MoncashAPI($id, $secret);

	//Les opérations qui s'en suivent

	$newId = "<nouveau client id>";
	$newSecret = "<nouveau client secret>";

	$moncash->setCredentials($newId, $newSecret);

	var_dump($moncash);

} catch(MoncashEasy\SDK\MoncashException $e) {
	echo "Erreur : ".$e->getMessage();
}

```


Extras
-----

Pour toutes suggestions ou problèmes rencontrées, contacter au schneiderchery7@gmail.com

Toutes améliorations et ajouts de fonctionnalités sont les bienvenues.
