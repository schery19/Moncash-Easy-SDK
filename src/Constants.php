<?php

namespace MoncashEasy\SDK;


class Constants {

	const SANDBOX_ENDPOINT = "https://sandbox.moncashbutton.digicelgroup.com/Api";

	const LIVE_ENDPOINT = "https://moncashbutton.digicelgroup.com/Api";

	const SANDBOX = "sandbox";

	const LIVE = "live";

	const OAUTH_TOKEN_URI = "/oauth/token";

	const PAYMENT_MAKER = "/v1/CreatePayment";

	const TRANSFERT = "/v1/Transfert";

	const SANDBOX_REDIRECT_URL = "https://sandbox.moncashbutton.digicelgroup.com/Moncash-middleware";

	const LIVE_REDIRECT_URL = "https://moncashbutton.digicelgroup.com/Moncash-middleware";

	const GATEWAY_URI = "/Payment/Redirect";

	const PAYMENT_ORDER_URI = "/v1/RetrieveOrderPayment";

	const PAYMENT_TRANSACTION_URI = "/v1/RetrieveTransactionPayment";

	const IMG_URI = "/resources/assets/images/";
}

?>
