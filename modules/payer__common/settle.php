<?php
require_once("../../config/config.inc.php");
require_once("payread_post_api.php");
$payerApi=new payread_post_api();
$payment = 'payer';
if($_GET['payer_payment_type']=='card'){
	$payment .= '_card';
	require_once("../$payment/$payment".".php");
	$payer = new Payer_card();
	$payerApi->setAgent(Configuration::get('PAYER_CARD_AGENTID'));
	$payerApi->setKeys(Configuration::get('PAYER_CARD_KEY1'),Configuration::get('PAYER_CARD_KEY2'));
} else if($_GET['payer_payment_type']=='bank'){
	$payment .= '_bank';
	require_once("../$payment/$payment".".php");
	$payer = new Payer_bank();
	$payerApi->setAgent(Configuration::get('PAYER_BANK_AGENTID'));
	$payerApi->setKeys(Configuration::get('PAYER_BANK_KEY1'),Configuration::get('PAYER_BANK_KEY2'));
} else if($_GET['payer_payment_type']=='invoice'){
	$payment .= '_invoice';
	require_once("../$payment/$payment".".php");
	$payer = new Payer_invoice();
	$payerApi->setAgent(Configuration::get('PAYER_INVOICE_AGENTID'));
	$payerApi->setKeys(Configuration::get('PAYER_INVOICE_KEY1'),Configuration::get('PAYER_INVOICE_KEY2'));
} else if($_GET['payer_payment_type']=='swish'){
	$payment .= '_swish';
	require_once("../$payment/$payment".".php");
	$payer = new Payer_swish();
	$payerApi->setAgent(Configuration::get('PAYER_SWISH_AGENTID'));
	$payerApi->setKeys(Configuration::get('PAYER_SWISH_KEY1'),Configuration::get('PAYER_SWISH_KEY2'));
} else {
	$payment .= '_all';
	require_once("../$payment/$payment".".php");
	$payer = new Payer_all();
	$payerApi->setAgent(Configuration::get('PAYER_ALL_AGENTID'));
	$payerApi->setKeys(Configuration::get('PAYER_ALL_KEY1'),Configuration::get('PAYER_ALL_KEY2'));
}
if ($payerApi->is_valid_ip()){
	if ($payerApi->is_valid_callback()){
		$cart = new Cart((int)($_GET['id_cart']));
		Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."orders SET module = '".$payment."' WHERE id_order = '".$orderid."' AND id_cart = '".intval($_GET['id_cart'])."'");
		$payer->validateOrder(intval($_GET['id_cart']),_PS_OS_PAYMENT_, $_GET['amount'], $payer->displayName, 'Payer-id: '.$_GET['payread_payment_id'], array(), null, false, $cart->secure_key);
		$order = new Order();
		$orderid = $order->getOrderByCartId(intval($_GET['id_cart']));
		if($_GET['payer_added_fee']){
			$fee = 0+$_GET['payer_added_fee'];
			$feenotax = $fee*0.8;
			Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."orders SET total_paid = (total_paid+".$fee."), total_paid_real = (total_paid_real+".$fee."), total_products = (total_products+".$feenotax."), total_products_wt = (total_products_wt+".$fee.") WHERE id_order = '".$orderid."' AND id_cart = '".intval($_GET['id_cart'])."'");
			Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."order_detail(id_order, product_id, product_name, product_quantity, product_price, tax_rate,product_attribute_id) VALUES('".$orderid."',0, 'Exp.avgift',1,'".$feenotax."',25,0)");
			die("TRUE:FEE");
		}
		die("TRUE");
	}
	die("FALSE:CALLBACK");
}
die("FALSE:IP");
?>