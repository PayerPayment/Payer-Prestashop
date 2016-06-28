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
} else if($_GET['payer_payment_type']=='enter'){
	$payment .= 'enter';
	require_once("../$payment/$payment".".php");
	$payer = new Payer_enter();
	$payerApi->setAgent(Configuration::get('PAYER_ENTER_AGENTID'));
	$payerApi->setKeys(Configuration::get('PAYER_ENTER_KEY1'),Configuration::get('PAYER_ENTER_KEY2'));
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
		die("TRUE");
	}
	die("FALSE:CALLBACK");
}
die("FALSE:IP");
?>
