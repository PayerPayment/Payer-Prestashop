<?php
/*
 *  LEGAL NOTICE
* Prestaworks� - http://www.prestaworks.com
Copyright (c) 2008
by Prestaworks
* Permission is hereby granted, to the buyer of this software to use it freely in association with prestashop.
* The buyer are free to use/edit/modify this software in anyway he/she see fit.
* The buyer are NOT allowed to redistribute this module in anyway or resell it or redistribute it to third party.
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

require_once(_PS_MODULE_DIR_.'payer__common/payread_post_api.php');
class Payer_wywallet extends PaymentModule {
	public function __construct(){
		$this->name = 'payer_wywallet';
		$this->tab = 'payments_gateways';
		$this->version = '1.6';
		$this->currencies = true;
		$this->currencies_mode = 'radio';
		parent::__construct();
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Payer WyWallet');
		$this->description = $this->l('Pay by wywallet with payer.');
	}
	public function install(){
		if (!parent::install() 
				OR !Configuration::updateValue('PAYER_WYWALLET_TEST', 1)
				OR !Configuration::updateValue('PAYER_WYWALLET_AGENTID', 1) 
				OR !Configuration::updateValue('PAYER_WYWALLET_KEY1', 1)
				OR !Configuration::updateValue('PAYER_WYWALLET_KEY2', 1) 
				OR !$this->registerHook('payment') 
				OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall(){
		if(!parent::uninstall() OR !Configuration::deleteByName('PAYER_WYWALLET_TEST') OR !Configuration::deleteByName('PAYER_WYWALLET_AGENTID') OR !Configuration::deleteByName('PAYER_WYWALLET_KEY1') OR !Configuration::deleteByName('PAYER_WYWALLET_KEY2'))
			return false;
		return true;
	}


	public function getContent(){
		$this->_html = '<h2>Payer</h2>';
		if (isset($_POST['submitSecurepay'])){
			if (!isset($_POST['test'])){
				$_POST['test'] = '1';
			}
			if (!sizeof($this->_postErrors)){
				Configuration::updateValue('PAYER_WYWALLET_AGENTID', $_POST['agentid']);
				Configuration::updateValue('PAYER_WYWALLET_KEY1', $_POST['key1']);
				Configuration::updateValue('PAYER_WYWALLET_KEY2', $_POST['key2']);
				Configuration::updateValue('PAYER_WYWALLET_TEST', intval($_POST['test']));
				$this->displayConf();
			}
			else {
				$this->displayErrors();
			}
		}

		$this->displayFormSettings();
		return $this->_html;
	}

	public function displayConf(){
		$this->_html .= '
		<div class="conf confirm">
		<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
		'.$this->l('Settings updated').'
		</div>';
	}

	public function displayFormSettings(){
		$conf = Configuration::getMultiple(array('PAYER_WYWALLET_TEST', 'PAYER_WYWALLET_AGENTID','PAYER_WYWALLET_KEY1','PAYER_WYWALLET_KEY2'));
		$payertest = array_key_exists('test', $_POST) ? $_POST['test'] : (array_key_exists('PAYER_WYWALLET_TEST', $conf) ? $conf['PAYER_WYWALLET_TEST'] : '');
		$agentid = array_key_exists('agentid', $_POST) ? $_POST['agentid'] : (array_key_exists('PAYER_WYWALLET_AGENTID', $conf) ? $conf['PAYER_WYWALLET_AGENTID'] : '');
		$key1 = array_key_exists('key1', $_POST) ? $_POST['key1'] : (array_key_exists('PAYER_WYWALLET_KEY1', $conf) ? $conf['PAYER_WYWALLET_KEY1'] : '');
		$key2 = array_key_exists('key2', $_POST) ? $_POST['key2'] : (array_key_exists('PAYER_WYWALLET_KEY2', $conf) ? $conf['PAYER_WYWALLET_KEY2'] : '');

		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset>
		<label>'.$this->l('Agentid').'</label>
		<div><input type="text" name="agentid" value="'.$agentid.'" /></div><br />
		<label>'.$this->l('Key 1').'</label>
		<div><input type="text" name="key1" value="'.$key1.'" /></div><br />
		<label>'.$this->l('Key 2').'</label>
		<div><input type="text" name="key2" value="'.$key2.'" /></div><br />
		<legend><img src="../img/admin/contact.gif" />'.$this->l('Settings').'</legend>
		<label>'.$this->l('Test mode').'</label>
		<div>
		<input type="radio" name="test" value="1" '.($payertest ? 'checked="checked"' : '').' /> '.$this->l('Yes').'
		<input type="radio" name="test" value="0" '.(!$payertest ? 'checked="checked"' : '').' /> '.$this->l('No').'
		</div><br />
		<br />
		<input type="submit" name="submitSecurepay" value="'.$this->l('Update settings').'" class="button" /></center>
		</fieldset>
		</form><br /><br />
		<fieldset class="width3">
		<legend><img src="../img/admin/warning.gif" />'.$this->l('Information').'</legend>
		'.$this->l('In order to use your Payer payment module, you have to configure your Payer account').'<br /><br />
		<br />
		</fieldset>';
	}

	public function hookPayment($params){
		$payerApi = new payread_post_api();

		global $smarty, $cookie;
		$address = new Address(intval($params['cart']->id_address_invoice));
		$customer = new Customer(intval($params['cart']->id_customer));

		$payerApi->setAgent(Configuration::get('PAYER_WYWALLET_AGENTID'));
		$payerApi->setKeys(Configuration::get('PAYER_WYWALLET_KEY1'),Configuration::get('PAYER_WYWALLET_KEY2'));

		$currency = $this->getCurrency();
		$currency = new Currency((int)($params['cart']->id_currency));
		$payerApi->set_currency($currency->iso_code);
		$lang = new Language((int)($cookie->id_lang));
		$payerApi->set_language($lang->iso_code);

		$payerApi->set_reference_id($params['cart']->id);

		if (!Validate::isLoadedObject($address) OR !Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($currency)){
			return $this->l('SecurePay error: (invalid address or customer)');
		}

		$products = $params['cart']->getProducts();
		$sub_amount = 0.0;

		$i=1;
		foreach($products as $key => $product){
			$procuctpriceinclvat=0;
			$i++;
			$products[$key]['name'] = str_replace('"', '\'', $product['name']);
			if (isset($product['attributes'])){
				$products[$key]['attributes'] = str_replace('"', '\'', $product['attributes']);
			}
			$products[$key]['name'] = htmlentities(utf8_decode($product['name']));
			$procuctpriceinclvat = number_format(Product::getPriceStatic(intval($product['id_product']), true, isset($product['id_product_attribute']) ? intval($product['id_product_attribute']) : NULL, 6, NULL, false, true, 1), 2, '.', '');
			$payerApi->add_freeform_purchase($i, $product['name'], $procuctpriceinclvat, number_format($product['rate'], 2, ".", ""), $product['cart_quantity']);
			$temp_amount = $procuctpriceinclvat * intval($product['cart_quantity']);
			$sub_amount = $sub_amount + $temp_amount;
		}

		$shipping=$params['cart']->getOrderShippingCost();

		$amount =$sub_amount+$shipping;
		$i++;
		$carrierData = new Carrier(intval($params['cart']->id_carrier), intval($params['cart']->id_lang));
		$carriername = ($carrierData->name == '0' ? Configuration::get('PS_SHOP_NAME'): $carrierData->name);
		$taxData = new Tax(intval($carrierData->id_tax_rules_group), intval($params['cart']->id_lang));
		$payerApi->add_freeform_purchase($i, $carriername, $shipping, number_format($taxData->rate, 2, ".", ""), '1');

		$discounts = $params['cart']->getDiscounts();
		$discountAmount = 0;
		foreach($discounts as $discount){
			$i++;
			$payerApi->add_freeform_purchase($i,$discount['description'],'-'.$discount['value_real'],((($discount['value_real']-$discount['value_tax_exc'])/$discount['value_tax_exc'])*100),'1');
			$discountAmount-=$discount['value_real'];
		}

		$giftAmount = 0;
		if($params['cart']->gift){
			$i++;
			$wrapping_fees = (float)(Configuration::get('PS_GIFT_WRAPPING_PRICE'));
			$wrapping_fees_tax = new Tax((int)(Configuration::get('PS_GIFT_WRAPPING_TAX')));
			$wrapping_fees *= 1 + (((float)($wrapping_fees_tax->rate) / 100));
			$wrapping_fees = Tools::convertPrice(Tools::ps_round($wrapping_fees, 2), Currency::getCurrencyInstance((int)($currency->id)));
			$wrapping_fees_tax = new Tax((int)(Configuration::get('PS_GIFT_WRAPPING_TAX')));
			$payerApi->add_freeform_purchase($i,"Inslagning",$wrapping_fees,$wrapping_fees_tax->rate,'1');
			$giftAmount = $wrapping_fees;
		}

		$amount = $amount+$discountAmount+$giftAmount;

		$first_name = $customer->firstname;
		$last_name  = $customer->lastname;
		$address1   = $address->address1;
		$address2   = $address->address2;
		$city       = $address->city;
		$zip        = $address->postcode;
		$email      = $customer->email;
		$phone      = $address->phone;
		$country    = strtolower(Country::getIsoById($address->id_country));

		$payerApi->add_buyer_info($first_name, $last_name, $address1, $address2, $zip, $city, $country, $phone, $phone, $phone, $email);

		$payerApi->add_payment_method("wywallet");
		if(Configuration::get('PAYER_WYWALLET_TEST')=='1'){
			$payerApi->set_test_mode('true');
			$payerApi->set_debug_mode('verbose');
		} else {
			$payerApi->set_test_mode('false');
		}

		$Auth_url = stripslashes(_PS_BASE_URL_.__PS_BASE_URI__ . "modules/payer__common/auth.php?id_cart=".intval($params['cart']->id).'&id_order_status=2&amount='.$amount);
		$Settle_url = stripslashes(_PS_BASE_URL_.__PS_BASE_URI__ . "modules/payer__common/settle.php?id_cart=".intval($params['cart']->id).'&id_order_status=2&amount='.$amount);
		$Shop_url = stripslashes(_PS_BASE_URL_.__PS_BASE_URI__ .'index.php?controller=history'.'&id_cart='.intval($params['cart']->id).'&id_module='.$this->id);

		$payerApi->set_authorize_notification_url($Auth_url);
		$payerApi->set_settle_notification_url($Settle_url);
		$payerApi->set_redirect_back_to_shop_url($Shop_url);
		$payerApi->set_success_redirect_url($Shop_url);

		$smarty->assign(array(
				'payread_agentid' => $payerApi->get_agentid(),
				'payread_xml_writer' => $payerApi->get_api_version(),
				'payread_data' => $payerApi->get_xml_data() ,
				'payread_checksum' => $payerApi->get_checksum(),
				'securepayUrl' => $payerApi->get_server_url()
		));

		return $this->display(__FILE__, 'payment.tpl');
	}

	function hookPaymentReturn($params){
		return $this->display(__FILE__, 'confirmation.tpl');
	}
}
?>