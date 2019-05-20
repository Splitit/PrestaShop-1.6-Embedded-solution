<?php

/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @license BSD 2 License
 * @since 1.6.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

define('SPLITIT_URL', 'https://www.splitit.com/register');

// Card Types
define('CC_VISA', 'VI');
define('CC_MASTER_CARD', 'MC');

//First Payment Options
define('PO_EQUAL', 'equal');
define('PO_SHIPPING_TAXES', 'shipping_taxes');
define('PO_SHIPPING', 'shipping');
define('PO_TAX', 'tax');
define('PO_PERCENTAGE', 'percentage');

//Installment Setup
define('IS_FIXED', 'fixed');
define('IS_DEPENDING_ON_CART', 'depending_on_cart');

//Show Installment On Pages
define('CATEGORY', 'category');
define('PRODUCT', 'product');
define('CART', 'cart');
define('CHECKOUT', 'checkout');

define('SPLITIT_SANDBOX_URL', 'https://web-api-sandbox.splitit.com');
define('SPLITIT_LIVE_URL', 'https://web-api.splitit.com');

include_once(dirname(__FILE__) . '/Api/ApiSplitit.php');

class Splitit extends PaymentModule {

    public $saved_credit_cards;
    public $api;
    public $errors = array();

    public function __construct() {
        $this->name = 'splitit';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Splitit';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Splitit');
        $this->description = $this->l('Accept payments by Splitit in easy installments.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your settings?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
        $this->module_key = '67b2601387e631764c9c8619a2e47c9f';

        $this->api = new ApiSplitit();
        $this->registerHook('actionOrderStatusPostUpdate');
        $this->registerHook('actionOrderSlipAdd');
    }

    public function install() {
        if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('header') || !$this->registerHook('paymentReturn') || !$this->registerHook('displayProductPriceBlock') || !$this->registerHook('displayProductButtons') || !$this->registerHook('backOfficeHeader') || !$this->registerHook('shoppingCartFooter') || !$this->registerHook('displayShoppingCart') || !$this->registerHook('displaySplititInstallment') || !$this->registerHook('displaySplititCategoryInstallment') || !Configuration::updateValue('SPLITIT_IS_ENABLED', 0) || !Configuration::updateValue('SPLITIT_API_KEY', '') || !Configuration::updateValue('SPLITIT_API_USER_NAME', '') || !Configuration::updateValue('SPLITIT_API_PASSWORD', '') || !Configuration::updateValue('SPLITIT_SANDBOX_MODE', 1) || !Configuration::updateValue('SPLITIT_DEBUG_MODE', 0)
                  /*|| !Configuration::updateValue('SPLITIT_ALLOWED_COUNTRIES', 1)
                  || !Configuration::updateValue('SPLITIT_SPECIFIC_COUNTRIES', '')
                  || !Configuration::updateValue('SPLITIT_TITLE', '')*/
                  || !Configuration::updateValue('SPLITIT_HELP_LINK_URL', 'https://s3.amazonaws.com/splitit-images-prod/learnmore/en-us/V1-USD.png')
                  || !Configuration::updateValue('SPLITIT_HELP_LINK_TITLE', $this->l('Tell me more'))
                  /*|| !Configuration::updateValue('SPLITIT_PAYMENT_ACTION', 'authorize')
                  || !Configuration::updateValue('SPLITIT_ORDER_STATUS', (int)Configuration::get('PS_OS_PAYMENT')) */ || !Configuration::updateValue('SPLITIT_CARD_TYPES', 'VI,MC') || !Configuration::updateValue('SPLITIT_FIRST_PAYMENT', 'equal')
                /* || !Configuration::updateValue('SPLITIT_PERCENTAGE_OF_ORDER', '50') */ || !Configuration::updateValue('SPLITIT_INSTALLMENT_SETUP', 'fixed') || !Configuration::updateValue('SPLITIT_FIXED_INSTALLMENT', '')
                /* || !Configuration::updateValue('SPLITIT_DEPENDING_ON_CART', '') */ || !Configuration::updateValue('SPLITIT_ENABLE_PRICE', 0) || !Configuration::updateValue('SPLITIT_INSTALLMENT_PRICE_ON_PAGES', '') || !Configuration::updateValue('SPLITIT_INSTALLMENT_COUNT', '') || !Configuration::updateValue('SPLITIT_INSTALLMENT_PRICE_TEXT', $this->l('No Interest'))
        /* || !$this->installDb() */) {
            return false;
        }

        $this->registerHook('displayMobileHeader');
        return true;
    }

    /**
     * Splitit's module database tables installation
     *
     * @return boolean Database tables installation result
     */
    public function uninstall() {
        if (!parent::uninstall() || !Configuration::deleteByName('SPLITIT_IS_ENABLED') || !Configuration::deleteByName('SPLITIT_API_KEY') || !Configuration::deleteByName('SPLITIT_API_USER_NAME') || !Configuration::deleteByName('SPLITIT_API_PASSWORD') || !Configuration::deleteByName('SPLITIT_SANDBOX_MODE') || !Configuration::deleteByName('SPLITIT_DEBUG_MODE')
                /*            || !Configuration::deleteByName('SPLITIT_ALLOWED_COUNTRIES')
                  || !Configuration::deleteByName('SPLITIT_SPECIFIC_COUNTRIES')
                  || !Configuration::deleteByName('SPLITIT_TITLE')
                  || !Configuration::deleteByName('SPLITIT_HELP_LINK')
                  || !Configuration::deleteByName('SPLITIT_HELP_LINK_TITLE')
                  || !Configuration::deleteByName('SPLITIT_PAYMENT_ACTION')
                  || !Configuration::deleteByName('SPLITIT_ORDER_STATUS')
                 */ || !Configuration::deleteByName('SPLITIT_CARD_TYPES') || !Configuration::deleteByName('SPLITIT_FIRST_PAYMENT')
                /* || !Configuration::deleteByName('SPLITIT_PERCENTAGE_OF_ORDER') */ || !Configuration::deleteByName('SPLITIT_INSTALLMENT_SETUP') || !Configuration::deleteByName('SPLITIT_FIXED_INSTALLMENT')
                /* || !Configuration::deleteByName('SPLITIT_DEPENDING_ON_CART') */ || !Configuration::deleteByName('SPLITIT_ENABLE_PRICE') || !Configuration::deleteByName('SPLITIT_INSTALLMENT_PRICE_ON_PAGES') || !Configuration::deleteByName('SPLITIT_INSTALLMENT_COUNT') || !Configuration::deleteByName('SPLITIT_INSTALLMENT_PRICE_TEXT')
        /* || !$this->unInstallDb() */) {
            return false;
        }

        return true;
    }

    /**
     * Splitit's module database tables uninstallation
     *
     * @return boolean Database tables uninstallation result
     */
    /* 	public function unInstallDb()
      {
      return Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'splitit_transaction`');
      }
     */

    public function hookDisplayMobileHeader() {
        return $this->hookHeader();
    }

    /**
     * Load Javascripts and CSS related to the Splitit module
     * Only loaded during the checkout process
     *
     * @return string HTML/JS Content
     */
    public function hookHeader() {

        /* Continue only if we are in the checkout process */
        if (Tools::getValue('controller') != 'order-opc' && (!($_SERVER['PHP_SELF'] == __PS_BASE_URI__ . 'order.php' || $_SERVER['PHP_SELF'] == __PS_BASE_URI__ . 'order-opc.php' || Tools::getValue('controller') == 'order' || Tools::getValue('controller') == 'orderopc' || Tools::getValue('step') == 3))) {
            return;
        }

        /* Load JS and CSS files through CCC */
        $this->context->controller->addCSS($this->_path . 'views/css/splitit.css');

        return '<script type="text/javascript" src="' . $this->_path . 'views/js/splitit.js"></script>';
    }

    /* process Splitit Partial refund */
    public function hookActionOrderSlipAdd($params){
        // Check if API is Enabled and API key is set
        if (!$this->active 
                || !Configuration::get('SPLITIT_IS_ENABLED') 
                || !Configuration::get('SPLITIT_API_KEY') 
                || !Configuration::get('SPLITIT_API_USER_NAME') 
                || !Configuration::get('SPLITIT_API_PASSWORD')
                /* || !Configuration::get('SPLITIT_CARD_TYPES') */ 
                || !Configuration::get('SPLITIT_FIXED_INSTALLMENT') 
                || Configuration::get('SPLITIT_IS_ENABLED') == 0 
                || Configuration::get('SPLITIT_API_KEY') == '' 
                || Configuration::get('SPLITIT_API_USER_NAME') == '' 
                || Configuration::get('SPLITIT_API_PASSWORD') == ''
                /* || Configuration::get('SPLITIT_CARD_TYPES') == '' */ 
                || Configuration::get('SPLITIT_FIXED_INSTALLMENT') == '') 
        {
            return;
        }
        
        $order_payment = OrderPayment::getByOrderId($params['order']->id);
        if(isset($order_payment[0])&&isset($order_payment[0]->payment_method)&&$order_payment[0]->payment_method!='Splitit'){
            return;
        }
        // die('proceeding at wrong place.');

        // if (!$this->context->cookie->sessionId) {
            $login = $this->login(FALSE);
        // }
        /*print_r($params);
        print_r($order_payment);
        var_dump($order_payment[0]->payment_method);
        die('--------sdakjfdhajksd');*/
        /*print_r($params);*/
        $transaction_id = Db::getInstance()->getValue('SELECT transaction_id FROM ' . _DB_PREFIX_ . 'order_payment op JOIN `' . _DB_PREFIX_ . 'orders` o ON (op.`order_reference` = o.`reference`) WHERE o.id_order = ' . (int) $params['order']->id);
        /*echo "transactionID==".$transaction_id;*/
        $amount=0.00;
        foreach ($params['productList'] as $prodId => $returnedItem) {
            $amount+=floatval($returnedItem['amount']);
        }
        $params = array(
            "RequestHeader" => array(
                "SessionId" => $this->context->cookie->sessionId,
            ),
            "InstallmentPlanNumber" => $transaction_id,
            "Amount" => array("Value" => $amount),
            "_RefundStrategy" => "FutureInstallmentsFirst"
        );
        $result = $this->api->doCurl($this->getApiUrl(), "InstallmentPlan/Refund", $params);
        if(!empty($result)){
            $result = json_decode($result, true);
            if (!$result) {
                $errorMsg = "";

                $errorCode = 503;
                $isErrorCode503Found = 0;
                foreach ($result["ResponseHeader"]["Errors"] as $key => $value) {
                    $errorMsg .= $value["ErrorCode"] . " : " . $value["Message"];
                    if ($value["ErrorCode"] == $errorCode) {
                        $isErrorCode503Found = 1;
                        break;
                    }
                }

                if ($isErrorCode503Found == 0) {
                    throw new Exception(($errorMsg));
                }
            } elseif (isset($result["serverError"])) {
                $errorMsg = $result["serverError"];
                throw new Exception(($errorMsg));
            }
        }
    }

    public function hookActionOrderStatusPostUpdate($params) {
//        throw new Exception("post order status");
        // Check if API is Enabled and API key is set
        if (!$this->active 
                || !Configuration::get('SPLITIT_IS_ENABLED') 
                || !Configuration::get('SPLITIT_API_KEY') 
                || !Configuration::get('SPLITIT_API_USER_NAME') 
                || !Configuration::get('SPLITIT_API_PASSWORD')
                /* || !Configuration::get('SPLITIT_CARD_TYPES') */ 
                || !Configuration::get('SPLITIT_FIXED_INSTALLMENT') 
                || Configuration::get('SPLITIT_IS_ENABLED') == 0 
                || Configuration::get('SPLITIT_API_KEY') == '' 
                || Configuration::get('SPLITIT_API_USER_NAME') == '' 
                || Configuration::get('SPLITIT_API_PASSWORD') == ''
                /* || Configuration::get('SPLITIT_CARD_TYPES') == '' */ 
                || Configuration::get('SPLITIT_FIXED_INSTALLMENT') == '') 
        {
            return;
        }
//        print_r($params);exit;
//        die('SELECT transaction_id FROM '._DB_PREFIX_.'order_payment op LEFT JOIN `'._DB_PREFIX_.'orders` o ON (op.`order_reference` = o.`reference`) WHERE o.id_order = '.(int)$params['id_order']);
//        echo "PS_OS_CANCELED==";
//        var_dump(($params['newOrderStatus']->id == Configuration::get('PS_OS_CANCELED')));
//        echo "PS_OS_REFUNDED==";
//        var_dump(($params['newOrderStatus']->id == Configuration::get('PS_OS_REFUND')));
//        print_r($this->login(FALSE));
        
        $order_payment = OrderPayment::getByOrderId((int) $params['id_order']);
        if(isset($order_payment[0])&&isset($order_payment[0]->payment_method)&&$order_payment[0]->payment_method!='Splitit'){
            return;
        }
        // die('proceeding at wrong place after update.');

        // if (!$this->context->cookie->sessionId) {
            $login = $this->login(FALSE);
        // }
        /*print_r($params);
        print_r($order_payment);
        var_dump($order_payment[0]->payment_method);
        die('--------sdakjfdhajksd');*/
//        die("this is it ======== session id=" . $this->context->cookie->sessionId);
        $transction_id = Db::getInstance()->getValue('SELECT transaction_id FROM ' . _DB_PREFIX_ . 'order_payment op JOIN `' . _DB_PREFIX_ . 'orders` o ON (op.`order_reference` = o.`reference`) WHERE o.id_order = ' . (int) $params['id_order']);
        $result = array();
        if ($transction_id) {
            if ($params['newOrderStatus']->id == Configuration::get('PS_OS_CANCELED')) {
                $params = array(
                    "RequestHeader" => array(
                        "SessionId" => $this->context->cookie->sessionId,
                    ),
                    "InstallmentPlanNumber" => $transction_id,
                    "RefundUnderCancelation" => "OnlyIfAFullRefundIsPossible"
                );
                $result = $this->api->doCurl($this->getApiUrl(), "InstallmentPlan/Cancel", $params);
//                print_r($result);exit;
            } elseif ($params['newOrderStatus']->id == Configuration::get('PS_OS_REFUND')) {
                $amount = Db::getInstance()->getValue('SELECT amount FROM ' . _DB_PREFIX_ . 'order_payment op JOIN `' . _DB_PREFIX_ . 'orders` o ON (op.`order_reference` = o.`reference`) WHERE o.id_order = ' . (int) $params['id_order']);
                $params = array(
                    "RequestHeader" => array(
                        "SessionId" => $this->context->cookie->sessionId,
                    ),
                    "InstallmentPlanNumber" => $transction_id,
                    "Amount" => array("Value" => $amount),
                    "_RefundStrategy" => "FutureInstallmentsFirst"
                );
                $result = $this->api->doCurl($this->getApiUrl(), "InstallmentPlan/Refund", $params);
//                print_r($result);exit;
            }
            if(!empty($result)){
            $result = json_decode($result, true);
            if (!$result) {
                $errorMsg = "";

                $errorCode = 503;
                $isErrorCode503Found = 0;
                foreach ($result["ResponseHeader"]["Errors"] as $key => $value) {
                    $errorMsg .= $value["ErrorCode"] . " : " . $value["Message"];
                    if ($value["ErrorCode"] == $errorCode) {
                        $isErrorCode503Found = 1;
                        break;
                    }
                }


                if ($isErrorCode503Found == 0) {
                    throw new Exception(($errorMsg));
                }
            } elseif (isset($result["serverError"])) {
                $errorMsg = $result["serverError"];
                throw new Exception(($errorMsg));
            }
        }
        }
    }

    /**
     * Display the SplitIt payment form
     *
     * @return string SplitIt Smarty template content
     */
    public function hookPayment($params) {
        // Check if API is Enabled and API key is set
        if (!$this->active 
            || !$this->checkCurrency($params['cart']) 
            || !Configuration::get('SPLITIT_IS_ENABLED') 
            || !Configuration::get('SPLITIT_API_KEY') 
            || !Configuration::get('SPLITIT_API_USER_NAME') 
            || !Configuration::get('SPLITIT_API_PASSWORD')
            /* || !Configuration::get('SPLITIT_CARD_TYPES') */ 
            || !Configuration::get('SPLITIT_FIXED_INSTALLMENT') 
            || Configuration::get('SPLITIT_IS_ENABLED') == 0 
            || Configuration::get('SPLITIT_API_KEY') == '' 
            || Configuration::get('SPLITIT_API_USER_NAME') == '' 
            || Configuration::get('SPLITIT_API_PASSWORD') == ''
            /* || Configuration::get('SPLITIT_CARD_TYPES') == '' */ 
            || Configuration::get('SPLITIT_FIXED_INSTALLMENT') == '') {
            return;
        }


        /* If the address check has been enabled by the merchant, we will transmitt the billing address to Stripe */
        if (isset($this->context->cart->id_address_invoice)) {
            $billing_address = new Address((int) $this->context->cart->id_address_invoice);
            if ($billing_address->id_state) {
                $state = new State((int) $billing_address->id_state);
                if (Validate::isLoadedObject($state)) {
                    $billing_address->state = $state->iso_code;
                }
            }
        }

        /* if (!empty($this->context->cookie->stripe_error)) {
          $this->smarty->assign('stripe_error', $this->context->cookie->stripe_error);
          $this->context->cookie->__set('stripe_error', null);
          } */

        $validation_url = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'index.php?process=validation&fc=module&module=splitit&controller=validate';

        // Get saved credit cards types from configuration
        //$saved_credit_cards = explode(',' , Configuration::get('SPLITIT_CARD_TYPES'));
        // First Installment

        $cart_total = $this->context->cart->getOrderTotal();

        //echo $this->context->cart->getPackageShippingCost();

        $currency = Currency::getCurrency((int) $this->context->cart->id_currency);

        // Installment Setup
        if (Configuration::get('SPLITIT_INSTALLMENT_SETUP') == IS_FIXED) {
            $fixed_installment = explode(',', Configuration::get('SPLITIT_FIXED_INSTALLMENT'));
            $front_drop = array();
            foreach ($fixed_installment as $installment) {
                $front_drop[$installment] = $installment . ' installments of ' . Tools::displayPrice(round($cart_total / $installment, 2), $currency);
            }
        }

        /* else if(Configuration::get('SPLITIT_INSTALLMENT_SETUP' == IS_DEPENDING_ON_CART)){

          } */

        $this->context->smarty->assign(array(
            'splitit_ps_version' => _PS_VERSION_,
            'validation_url' => $validation_url,
            'credit_cards' => SplitIt::getCreditCards(),
            //'saved_credit_cards'	=> $saved_credit_cards,
            'months' => Splitit::getMonths(),
            'years' => Splitit::getYears(),
            'installments' => $front_drop,
            'path' => $this->_path
        ));

        $outputHtml = $this->getInstallmentPriceLabel(CHECKOUT, $this->context->cart->getOrderTotal());
        if (!$outputHtml) {
            return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
        }

        $this->smarty->assign('outputHtml', $outputHtml);

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    public function checkCurrency($cart) {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check technical requirements to make sure the splitit's module will work properly
     *
     * @return array Requirements tests results
     */
    public function checkRequirements() {
        $tests = array('result' => true);
        $tests['curl'] = array('name' => $this->l('PHP cURL extension must be enabled on your server'), 'result' => extension_loaded('curl'));
        if (Configuration::get('SPLITIT_IS_ENABLED')) {
            $tests['ssl'] = array('name' => $this->l('SSL must be enabled on your store (before entering Live mode)'), 'result' => Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && Tools::strtolower($_SERVER['HTTPS']) != 'off'));
        }
        $tests['php52'] = array('name' => $this->l('Your server must run PHP 5.2 or greater'), 'result' => version_compare(PHP_VERSION, '5.2.0', '>='));

        foreach ($tests as $k => $test) {
            if ($k != 'result' && !$test['result']) {
                $tests['result'] = false;
            }
        }

        return $tests;
    }

    /**
     * Display the Back-office interface of the Splitit's module
     *
     * @return string HTML/JS Content
     */
    public function getContent() {
        $output = '<script type="text/javascript" src="' . $this->_path . 'views/js/splitit-admin.js"></script>
	  	<link href="' . $this->_path . 'views/css/splitit-admin.css" rel="stylesheet" type="text/css" media="all" />';

        //$requirements = $this->checkRequirements();
        $errors = array();

        /* Update Configuration Values when settings are updated */
        if (Tools::isSubmit('submitSplitit')) {
            if (empty($errors)) {
                $card_types = !empty(Tools::getValue('card_types')) ? Tools::getValue('card_types') : "";

                if (!empty($card_types)) {
                    $card_types = implode(',', $card_types);
                }

                $configuration_values = array(
                    'SPLITIT_IS_ENABLED' => Tools::getValue('is_enabled'),
                    'SPLITIT_API_KEY' => Tools::getValue('api_key'),
                    'SPLITIT_API_USER_NAME' => Tools::getValue('api_user_name'),
                    'SPLITIT_API_PASSWORD' => Tools::getValue('api_password'),
                    'SPLITIT_SANDBOX_MODE' => Tools::getValue('sandbox_mode'),
                    'SPLITIT_DEBUG_MODE' => Tools::getValue('debug_mode'),
                    //'SPLITIT_ALLOWED_COUNTRIES' => Tools::getValue('allowed_countries'),
                    //'SPLITIT_SPECIFIC_COUNTRIES' => implode(',' , Tools::getValue('specific_countries')),
                    // 'SPLITIT_TITLE' => Tools::getValue('title'),
                    'SPLITIT_HELP_LINK_URL' => Tools::getValue('help_link_url'),
                    'SPLITIT_HELP_LINK_TITLE' => Tools::getValue('help_link_title'),
                    // 'SPLITIT_PAYMENT_ACTION' => Tools::getValue('payment_action'), 
                    //'SPLITIT_ORDER_STATUS' => Tools::getValue('order_status'),
                    'SPLITIT_CARD_TYPES' => $card_types,
                    //'SPLITIT_FIRST_PAYMENT' => Tools::getValue('first_payment'),
                    //'SPLITIT_PERCENTAGE_OF_ORDER' => Tools::getValue('percentage_of_order'),
                    //'SPLITIT_INSTALLMENT_SETUP' => Tools::getValue('installment_setup'),
                    //'SPLITIT_FIXED_INSTALLMENT' => Tools::getValue('fixed_installment'),
                    //'SPLITIT_DEPENDING_ON_CART' => Tools::getValue('depending_on_cart'),
                    'SPLITIT_ENABLE_PRICE' => Tools::getValue('enable_price'),
                    //'SPLITIT_INSTALLMENT_PRICE_ON_PAGES' => implode(',' , Tools::getValue('installment_price_on_pages')),
                    'SPLITIT_INSTALLMENT_COUNT' => Tools::getValue('installemnt_count'),
                    'SPLITIT_INSTALLMENT_PRICE_TEXT' => Tools::getValue('price_text'),
                );

                $installment_price_on_pages = (!empty(Tools::getValue('installment_price_on_pages'))) ? Tools::getValue('installment_price_on_pages') : '';
                if (!empty($installment_price_on_pages)) {
                    $installment_price_on_pages = implode(',', $installment_price_on_pages);

                    $configuration_values['SPLITIT_INSTALLMENT_PRICE_ON_PAGES'] = $installment_price_on_pages;
                }


                // Specific Countries
                /* 				$allowed_countries = Tools::getValue('allowed_countries');
                  if(!$allowed_countries){
                  $specific_countries = (!empty(Tools::getValue('specific_countries'))) ? Tools::getValue('specific_countries') : '';
                  if(!empty($specific_countries)){

                  $specific_countries = implode(',' , $specific_countries);

                  $configuration_values['SPLITIT_SPECIFIC_COUNTRIES'] = $specific_countries;
                  }
                  } */

                // Installment Seturp Fixed or On Cart Total basis
                /* 				$installment_setup = Tools::getValue('installment_setup');

                  if($installment_setup == IS_FIXED){
                  $fixed_installment = implode(',' , Tools::getValue('fixed_installment'));

                  $configuration_values['SPLITIT_FIXED_INSTALLMENT'] = $fixed_installment;

                  } else if($installment_setup == IS_DEPENDING_ON_CART){

                  // Get setting for Cart basis
                  //
                  } */

                $fixed_installment = (!empty(Tools::getValue('fixed_installment'))) ? Tools::getValue('fixed_installment') : '';
                if (!empty($fixed_installment)) {
                    $fixed_installment = implode(',', Tools::getValue('fixed_installment'));
                    $configuration_values['SPLITIT_FIXED_INSTALLMENT'] = $fixed_installment;
                }

                foreach ($configuration_values as $configuration_key => $configuration_value) {
                    Configuration::updateValue($configuration_key, $configuration_value);
                }

                $this->context->smarty->assign('splitit_save_success', true);
            }
        }


        //$specific_countries = explode(',' , Configuration::get('SPLITIT_SPECIFIC_COUNTRIES'));

        $this->context->smarty->assign(array(
            'countries' => Country::getCountries(Configuration::get('PS_LANG_DEFAULT'), false),
            'credit_cards' => SplitIt::getCreditCards(),
            'first_payemnt_options' => SplitIt::getFirstPaymentOptions(),
            'installments' => Splitit::getInstallments(),
            'installmentSetups' => Splitit::getInstallmentSetup(),
            'show_on_pages' => Splitit::getPages(),
            //'payment_methods' => Splitit::getPaymentMethods(),
            'split_url' => SPLITIT_URL,
            'is_enabled' => Configuration::get('SPLITIT_IS_ENABLED'),
            'api_key' => Configuration::get('SPLITIT_API_KEY'),
            'api_user_name' => Configuration::get('SPLITIT_API_USER_NAME'),
            'api_password' => Configuration::get('SPLITIT_API_PASSWORD'),
            'sandbox_mode' => Configuration::get('SPLITIT_SANDBOX_MODE'),
            'debug_mode' => Configuration::get('SPLITIT_DEBUG_MODE'),
            //'allowed_countries' => Configuration::get('SPLITIT_ALLOWED_COUNTRIES'),
            //'specific_countries' => explode(',' , Configuration::get('SPLITIT_SPECIFIC_COUNTRIES')),
            // 'title' => Configuration::get('SPLITIT_TITLE'),
            'help_link_url' => Configuration::get('SPLITIT_HELP_LINK_URL'),
            'help_link_title' => Configuration::get('SPLITIT_HELP_LINK_TITLE'),
            // 'payment_action' => Configuration::get('SPLITIT_PAYMENT_ACTION'),
            // 'order_status' => Configuration::get('SPLITIT_ORDER_STATUS'), 
            'card_types' => explode(',', Configuration::get('SPLITIT_CARD_TYPES')),
            //'first_payment' => Configuration::get('SPLITIT_FIRST_PAYMENT'),
            //'percentage_of_order' => Configuration::get('SPLITIT_PERCENTAGE_OF_ORDER'),
            //'installment_setup' => Configuration::get('SPLITIT_INSTALLMENT_SETUP'),
            'fixed_installment' => explode(',', Configuration::get('SPLITIT_FIXED_INSTALLMENT')),
            //'depending_on_cart' => Configuration::get('SPLITIT_DEPENDING_ON_CART'),
            'enable_price' => Configuration::get('SPLITIT_ENABLE_PRICE'),
            'installment_price_on_pages' => explode(',', Configuration::get('SPLITIT_INSTALLMENT_PRICE_ON_PAGES')),
            'installemnt_count' => Configuration::get('SPLITIT_INSTALLMENT_COUNT'),
            'price_text' => Configuration::get('SPLITIT_INSTALLMENT_PRICE_TEXT'),
            'baseUrl' => Tools::getHttpHost(true) . __PS_BASE_URI__
        ));

        $output .= $this->display(__FILE__, 'views/templates/admin/back_office.tpl');

        return $output;
    }

    // Splitit Login
    public function login($reqInit = true) {
        $api_key = Configuration::get('SPLITIT_API_KEY');
        $api_user_name = Configuration::get('SPLITIT_API_USER_NAME');
        $api_password = Configuration::get('SPLITIT_API_PASSWORD');

        /*echo "api_key==$api_key";
        echo "\n api_user_name==$api_user_name";
        echo "\n api_password==$api_password";
        die('======THATS IT========');*/
        
        // Check if request is coming from frontend only
        if ($reqInit === true && $this->isCartEmpty()) {
            return array(
                'status' => false,
                'code' => "4",
                'message' => 'Cart Empty',
                'redirect' => $this->context->link->getPagelink('order')
            );
        }

/*        // Check if credintials coming from from or configuration settings.
        if (empty($api_key) || empty($api_user_name) || empty($api_password)) {
            $api_key = Configuration::get('SPLITIT_API_KEY');
            $api_user_name = Configuration::get('SPLITIT_API_USER_NAME');
            $api_password = Configuration::get('SPLITIT_API_PASSWORD');
        }*/

        $result = $this->api->login(
                $this->getApiUrl(), array(
            //'ApiKey' => $this->getConfigData('api_terminal_key', $storeId),
            'UserName' => $api_user_name,
            'Password' => $api_password,
            'TouchPoint' => array("Code" => "PrestaShop", "Version" => "1.0")
                )
        );

        // Check is Logedin
        if ($result['status'] === true && $reqInit === true) {
            // Call Initiate API
            $result = $this->api->installmentPlanInit($this->getApiUrl());
        }

        //die(Tools::jsonEncode($result));
        return $result;
    }

    public function getInstallmentPlans() {
        if (!Tools::getIsset('selectedInstallment') || (Tools::getIsset('selectedInstallment') && Tools::getValue('selectedInstallment') == '')) {
            $result = array(
                'status' => false,
                'code' => 2,
                'message' => $this->l('Please select installment plans')
            );

            return $result;
        } else {
            $selectedInstallment = Tools::getValue('selectedInstallment');

            $existing_installments = explode(',', Configuration::get('SPLITIT_FIXED_INSTALLMENT'));
            if (!in_array($selectedInstallment, $existing_installments)) {
                $result = array(
                    'status' => false,
                    'code' => 2,
                    'message' => $this->l('Invalid installment plan selected')
                );

                return $result;
            }

            // Check if session exist or not
            if (!isset($this->context->cookie->sessionId) || !isset($this->context->cookie->installmentPlanNumber) || !isset($this->context->cookie->installmentPlanInfoUrl)) {
                $result = $this->login();

                if ($result['status'] == false) {
                    return $result;
                }
            }

            // Check if request is coming from frontend onlye
            if ($this->isCartEmpty()) {
                return array(
                    'status' => false,
                    'code' => "4",
                    'message' => 'Cart Empty',
                    'redirect' => $this->context->link->getPagelink('order')
                );
            }

            // Get Installment Plans Details
            $result = $this->api->getInstallmentPlans($this->getInstallmentPlanUrl($selectedInstallment));

            if (isset($this->context->cookie->session_expired) && $this->context->cookie->session_expired == true) {
                $result = $this->login();

                if ($result['status'] == false) {
                    return $result;
                }

                // Get Installment Plans Details
                $result = $this->api->getInstallmentPlans($this->getInstallmentPlanUrl($selectedInstallment));
            }

            $this->context->cookie->__set('session_expired', false);

            return $result;
        }
    }

    /**
     * Validate Form Fields
     */
    public function validateFormFields() {
        $errors = array();

        /* $cc_type = (!empty(Tools::getValue('cc_type'))) ? Tools::getValue('cc_type') : ''; */
        $cc_number = (!empty(Tools::getValue('cc_number'))) ? Tools::getValue('cc_number') : '';
        $expiration_month = (!empty(Tools::getValue('expiration_month'))) ? Tools::getValue('expiration_month') : '';
        $expiration_yr = (!empty(Tools::getValue('expiration_yr'))) ? Tools::getValue('expiration_yr') : '';
        $cc_cid = (!empty(Tools::getValue('cc_cid'))) ? Tools::getValue('cc_cid') : '';
        $installments_no = (!empty(Tools::getValue('installments_no'))) ? Tools::getValue('installments_no') : '';
        $terms = (!empty(Tools::getValue('terms'))) ? Tools::getValue('terms') : '';

        /* if(empty($cc_type)){
          $errors['cc_type'] = $this->l('Please select Credit Card Type');
          } */
        if (empty($cc_number)) {
            $errors['cc_number'] = $this->l('Please enter credit card number');
        }
        if (empty($expiration_month)) {
            $errors['expiration_month'] = $this->l('Please select expiration month');
        }
        if (empty($expiration_yr)) {
            $errors['expiration_yr'] = $this->l('Please select expiration year');
        }
        if (empty($cc_cid)) {
            $errors['cc_cid'] = $this->l('Please enter CVV Number');
        }
        if (empty($installments_no)) {
            $errors['installments_no'] = $this->l('Please select number of installment');
        }
        if (empty($terms)) {
            $errors['terms'] = $this->l('Please agree to term and conditions');
        }

        if (empty($errors)) {
            return true;
        }

        $this->errors = $errors;

        return false;
    }

    /**
     * Process a payment
     *
     */
    public function processPayment() {
        if ($this->isCartEmpty()) {
            return array(
                'status' => false,
                'code' => "4",
                'message' => 'Cart Empty',
                'redirect' => $this->context->link->getPagelink('order')
            );
        }

        // Check if API is Enabled and API key is set
        if (!$this->active || !Configuration::get('SPLITIT_IS_ENABLED') || !Configuration::get('SPLITIT_API_KEY') || !Configuration::get('SPLITIT_API_USER_NAME') || !Configuration::get('SPLITIT_API_PASSWORD')
                /* || !Configuration::get('SPLITIT_CARD_TYPES') */ || !Configuration::get('SPLITIT_FIXED_INSTALLMENT') || Configuration::get('SPLITIT_IS_ENABLED') == 0 || Configuration::get('SPLITIT_API_KEY') == '' || Configuration::get('SPLITIT_API_USER_NAME') == '' || Configuration::get('SPLITIT_API_PASSWORD') == ''
                /* || Configuration::get('SPLITIT_CARD_TYPES') == '' */ || Configuration::get('SPLITIT_FIXED_INSTALLMENT') == '') {
            return array(
                'status' => false,
                'code' => '2',
                'message' => $this->l('There was a problem with your payment')
            );
        }

        // Validating form fields
        if (!$this->validateFormFields()) {
            return array(
                'status' => false,
                'code' => '3',
                'errors' => $this->errors,
                'message' => $this->l('There was a problem with your payment')
            );
        }


        if (!isset($this->context->cookie->sessionId) || !isset($this->context->cookie->installmentPlanNumber)) {
            $result = $this->login();

            if ($result['status'] == false) {
                return $result;
            }
        }

        /* $cc_type = (!empty(Tools::getValue('cc_type'))) ? Tools::getValue('cc_type') : ''; */
        $cc_number = (!empty(Tools::getValue('cc_number'))) ? Tools::getValue('cc_number') : '';
        $expiration_month = (!empty(Tools::getValue('expiration_month'))) ? Tools::getValue('expiration_month') : '';
        $expiration_yr = (!empty(Tools::getValue('expiration_yr'))) ? Tools::getValue('expiration_yr') : '';
        $cc_cid = (!empty(Tools::getValue('cc_cid'))) ? Tools::getValue('cc_cid') : '';
        $installments_no = (!empty(Tools::getValue('installments_no'))) ? Tools::getValue('installments_no') : '';

        $cc_number = str_replace(" ", "", $cc_number);

        try {
            $data = array(
                'num_installments' => $installments_no,
                'cc_cvv' => $cc_cid,
                'cc_holder_name' => $this->context->cookie->customer_firstname . " " . $this->context->cookie->customer_lastname,
                'cc_number' => $cc_number,
                'cc_exp_month' => $expiration_month,
                'cc_exp_year' => $expiration_yr,
            );

            if (isset($this->context->cookie->installmentPlanNumber)) {
                if (class_exists('Logger')) {
                    Logger::addLog($this->l('Creating Plan for IPN: ' . $this->context->cookie->installmentPlanNumber), 1, null, 'Cart', (int) $this->context->cart->id, true);
                }
            }

            $result = $this->api->createInstallmentPlan($this->getApiUrl(), $data);

            if (isset($this->context->cookie->installmentPlanNumber)) {
                if (class_exists('Logger')) {
                    Logger::addLog($this->l('Plan Created for IPN: ' . $this->context->cookie->installmentPlanNumber), 1, null, 'Cart', (int) $this->context->cart->id, true);
                }
            }

            /* $cards = Splitit::getCreditCards();
              $months = Splitit::getMonths();
              $years = Splitit::getYears(); */

            if ($result['status'] == true) {
                $message = $this->l('Split Transaction Details:') . "\n\n" .
                        $this->l('Splitit Transaction ID:') . ' ' . $this->context->cookie->installmentPlanNumber . "\n" .
                        $this->l('No of Installments:') . ' ' . $installments_no . "\n" .
                        $this->l('Amount:') . ' ' . $this->context->cart->getOrderTotal() . "\n" .
                        $this->l('Status:') . " Paid\n" .
                        $this->l('Processed on:') . ' ' . date('Y-m-d H:i:s') . "\n" .
                        $this->l('Currency:') . ' ' . Tools::strtoupper($this->context->currency->iso_code) . "\n";
                /* $this->l('Credit card:').' '.$cards[$cc_type]."\n";
                  $this->l('Credit card:').' '.$cards[$cc_type].' ('.$this->l('Exp.:').' '.$months[$expiration_month].'/'.$years[$expiration_yr].')'."\n"; */

                $order_status = (int) Configuration::get('PS_OS_PAYMENT');

                if (isset($this->context->cookie->installmentPlanNumber)) {
                    if (class_exists('Logger')) {
                        Logger::addLog($this->l('Creating Order in Database for IPN: ' . $this->context->cookie->installmentPlanNumber), 1, null, 'Cart', (int) $this->context->cart->id, true);
                    }
                }

                // Get Total Price
                $result = $this->api->getInstallmentPlanInfo(
                        $this->getApiUrl(), array(
                    'RequestHeader' => array(
                        'SessionId' => $this->context->cookie->sessionId
                    ),
                    'QueryCriteria' => array(
                        'InstallmentPlanNumber' => $this->context->cookie->installmentPlanNumber
                    )
                        )
                );

                if ($result["status"] != true) {
                    return $result;
                }

                $totalPrice = $result["message"];

                /* Create the PrestaShop order in database */
                //$this->validateOrder((int)$this->context->cart->id, (int)$order_status, $this->context->cart->getOrderTotal(), $this->displayName, $message, array(), null, false, $this->context->customer->secure_key);
                $this->validateOrder((int) $this->context->cart->id, (int) $order_status, $totalPrice, $this->displayName, $message, array(), null, false, $this->context->customer->secure_key);

                if (isset($this->context->cookie->installmentPlanNumber)) {
                    if (class_exists('Logger')) {
                        Logger::addLog($this->l('Order created in Database for IPN: ' . $this->context->cookie->installmentPlanNumber), 1, null, 'Cart', (int) $this->context->cart->id, true);
                    }
                }

                /** @since 1.5.0 Attach the Splitit Transaction ID to this Order */
                if (version_compare(_PS_VERSION_, '1.5', '>=')) {
                    $new_order = new Order((int) $this->currentOrder);
                    if (Validate::isLoadedObject($new_order)) {
                        $payment = $new_order->getOrderPaymentCollection();
                        if (isset($payment[0])) {
                            $payment[0]->transaction_id = pSQL($this->context->cookie->installmentPlanNumber);
                            $payment[0]->save();
                        }
                    }
                }


                /* 			Db::getInstance()->Execute('
                  INSERT INTO '._DB_PREFIX_.'splitit_transaction (type, id_cart, id_order,
                  id_transaction, amount, status, currency, cc_type, mode, date_add)
                  VALUES (\'payment\', '.(int)$this->context->cart->id.', '.(int)$this->currentOrder.', \''.pSQL($this->context->cookie->installmentPlanNumber).'\',
                  \''.($this->context->cart->getOrderTotal()).'\', \''.('paid').'\', \''.pSQL($this->context->currency->iso_code).'\',
                  \''.pSQL($cc_type).'\', \''.(Configuration::get('SPLITIT_SANDBOX_MODE') == '1' ? 'test' : 'live').'\', NOW())'); */

                $order = new Order($this->currentOrder); // Second parameter is id_lang, not required

                if (isset($this->context->cookie->installmentPlanNumber)) {
                    if (class_exists('Logger')) {
                        Logger::addLog($this->l('Updating Order reference no ' . $order->reference . ' for IPN: ' . $this->context->cookie->installmentPlanNumber), 1, null, 'Cart', (int) $this->context->cart->id, true);
                    }
                }

                $updateStatus = $this->api->updateRefOrderNumber($this->getApiUrl(), $order);
                if ($updateStatus['status'] == false) {
                    $result['message'] .= "," . $updateStatus["message"];
                }

                if (isset($this->context->cookie->installmentPlanNumber)) {
                    if (class_exists('Logger')) {
                        Logger::addLog($this->l('Order reference no ' . $order->reference . ' for IPN: ' . $this->context->cookie->installmentPlanNumber . ' updated successfully.'), 1, null, 'Cart', (int) $this->context->cart->id, true);
                    }
                }

                /* Redirect the user to the order confirmation page / history */
                $redirect = __PS_BASE_URI__ . 'index.php?controller=order-confirmation&id_cart=' . (int) $this->context->cart->id . '&id_module=' . (int) $this->id . '&id_order=' . (int) $this->currentOrder . '&key=' . $this->context->customer->secure_key;

                $result['redirect'] = $redirect;

                return $result;
            } else {
                return $result;
                // Transaction Failed Show error
            }

            // catch the stripe error the correct way.
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (class_exists('Logger')) {
                Logger::addLog($this->l('Splitit - Payment transaction failed') . ' ' . $message, 1, null, 'Cart', (int) $this->context->cart->id, true);
            }

            $result['message'] = $result['message'] . '<br/>' . $message;
            return $result;
        }
    }

    public function hookBackOfficeHeader() {

        /* Continue only if we are on the order's details page (Back-office) */
        if (!Tools::getIsset('vieworder') || !Tools::getIsset('id_order')) {
            return;
        }

        /* Check if the order was paid with Stripe and display the transaction details */
        /* if (Db::getInstance()->getValue('SELECT module FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)Tools::getValue('id_order')) == $this->name)
          {
          $currency = $this->context->currency;
          $c_char = $currency->sign;
          $output = '
          <script type="text/javascript">
          $(document).ready(function() {
          var appendEl;
          if ($(\'select[name=id_order_state]\').is(":visible")) {
          appendEl = $(\'select[name=id_order_state]\').parents(\'form\').after($(\'<div/>\'));
          } else {
          appendEl = $("#status");
          }
          $(\'<fieldset'.(_PS_VERSION_ < 1.5 ? ' style="width: 400px;"' : '').'><legend><img src="../img/admin/money.gif" alt="" />'.$this->l('Splitit Payment Details').'</legend>';

          $output .= $this->l('Splitit Transaction ID:').' '.Tools::safeOutput($stripe_transaction_details['id_transaction']).'<br /><br />'.
          $this->l('Status:').' <span style="font-weight: bold; color: '.($stripe_transaction_details['status'] == 'paid' ? 'green;">'.$this->l('Paid') : '#CC0000;">'.$this->l('Unpaid')).'</span><br />'.
          $this->l('Amount:').' '.Tools::displayPrice($stripe_transaction_details['amount']).'<br />'.
          $this->l('Processed on:').' '.Tools::safeOutput($stripe_transaction_details['date_add']).'<br />'.
          $this->l('Credit card:').' '.Tools::safeOutput($stripe_transaction_details['cc_type']).' ('.$this->l('Exp.:').' '.Tools::safeOutput($stripe_transaction_details['cc_exp']).')<br />'.
          $this->l('Last 4 digits:').' '.sprintf('%04d', $stripe_transaction_details['cc_last_digits']).' ('.$this->l('CVC Check:').' '.($stripe_transaction_details['cvc_check'] ? $this->l('OK') : '<span style="color: #CC0000; font-weight: bold;">'.$this->l('FAILED').'</span>').')<br />'.
          $this->l('Processing Fee:').' '.Tools::displayPrice($stripe_transaction_details['fee']).'<br /><br />'.
          $this->l('Mode:').' <span style="font-weight: bold; color: '.($stripe_transaction_details['mode'] == 'live' ? 'green;">'.$this->l('Live') : '#CC0000;">'.$this->l('Test (You will not receive any payment, until you enable the "Live" mode)')).'</span>';

          $output = "<p>This is Test Order</p>";

          return $output;
          } */
    }

    /**
     * Display a confirmation message after an order has been placed
     *
     * @param array Hook parameters
     */
    public function hookPaymentReturn($params) {
        if (!isset($params['objOrder']) || ($params['objOrder']->module != $this->name)) {
            return false;
        }

        if ($params['objOrder'] && Validate::isLoadedObject($params['objOrder']) && isset($params['objOrder']->valid)) {
            $this->smarty->assign(
                    'splitit_order', array(
                'reference' => isset($params['objOrder']->reference) ? $params['objOrder']->reference : '#' . sprintf('%06d', $params['objOrder']->id),
                'valid' => $params['objOrder']->valid)
            );
        }

        return $this->display(__FILE__, 'views/templates/hook/order-confirmation.tpl');
    }

    public function getInstallmentPlanUrl($installments) {
        return $this->context->cookie->installmentPlanInfoUrl . '&NumberOfInstallments=' . $installments;
    }

    public function getApiUrl() {
        if (Configuration::get('SPLITIT_SANDBOX_MODE') && Configuration::get('SPLITIT_SANDBOX_MODE') == 1) {
            return SPLITIT_SANDBOX_URL;
        }
        return SPLITIT_LIVE_URL;
    }

    public static function getMonths() {
        return array(
            '1' => '01 January',
            '2' => '02 February',
            '3' => '03 March',
            '4' => '04 April',
            '5' => '05 May',
            '6' => '06 June',
            '7' => '07 July',
            '8' => '08 August',
            '9' => '09 September',
            '10' => '10 October',
            '11' => '11 November',
            '12' => '12 December'
        );
    }

    public static function getYears() {
        $years = array();

        $year = date('Y');

        for ($i = $year; $i <= ($year + 10); $i++) {
            $years[$i] = $i;
        }

        return $years;
    }

    public static function getPages() {
        return array(
            CATEGORY => 'Category Page',
            PRODUCT => 'Product Page',
            CART => 'Shopping Cart',
            CHECKOUT => 'Checkout Page'
        );
    }

    public static function getInstallmentSetup() {
        return array(
            IS_FIXED => 'Fixed',
            IS_DEPENDING_ON_CART => 'Depending on cart total'
        );
    }

    public static function getCreditCards() {
        return array(
            CC_VISA => 'VISA',
            CC_MASTER_CARD => 'MasterCard'
        );
    }

    public static function getFirstPaymentOptions() {
        return array(
            PO_EQUAL => 'Equal to Monthly Payment',
            PO_SHIPPING_TAXES => 'Add Shipping & Taxes',
            PO_SHIPPING => 'Add Shipping',
            PO_TAX => 'Add Taxes',
            PO_PERCENTAGE => 'Equal to percentage of the order [X]'
        );
    }

    public static function getInstallments() {
        $installments = array();

        for ($i = 1; $i <= 12; $i++) {
            $installments[$i] = $i . ' installments';
        }

        return $installments;
    }

    public function hookDisplayProductPriceBlock($params) {
        $curr_controler = !empty(Tools::getValue('controller')) ? Tools::getValue('controller') : '';
        if (empty($curr_controler) || $curr_controler != CATEGORY) {
            return;
        }

        if (isset($params["product"]->price) && !empty($params["product"]->price)) {
            $price = $params["product"]->price;
        } elseif (isset($params["product"]["price"]) && !empty($params["product"]["price"])) {
            $price = $params["product"]["price"];
        } else {
            if (!isset($params["product"])) {
                return;
            }
        }

        $output = $this->getInstallmentPriceLabel(CATEGORY, $price);

        if (!$output) {
            return;
        }

        $this->smarty->assign('outputHtml', $output);

        return $this->display(__FILE__, 'views/templates/hook/display-productpriceblock.tpl');
    }

    public function hookDisplayProductButtons($params) {
        $curr_controler = !empty(Tools::getValue('controller')) ? Tools::getValue('controller') : '';
        if (empty($curr_controler) || $curr_controler != PRODUCT) {
            return;
        }

        $outputHtml = $this->getInstallmentPriceLabel(PRODUCT, $params["product"]->price);

        if (!$outputHtml) {
            return;
        }

        $this->smarty->assign('outputHtml', $outputHtml);

        return $this->display(__FILE__, 'views/templates/hook/display-productbuttons.tpl');
    }

    // Display Installment Price on Shopoing Cart Page

    public function hookDisplayShoppingCart($params) {
        $outputHtml = $this->getInstallmentPriceLabel(CART, $params["total_price"]);

        if (!$outputHtml) {
            return;
        }

        $this->smarty->assign('outputHtml', $outputHtml);

        return $this->display(__FILE__, 'views/templates/hook/display-shoppingcart.tpl');
    }

    public function getInstallmentPriceLabel($page, $price) {
        if (!$this->active || !Configuration::get('SPLITIT_IS_ENABLED') || !Configuration::get('SPLITIT_ENABLE_PRICE') || !Configuration::get('SPLITIT_INSTALLMENT_PRICE_ON_PAGES') || !Configuration::get('SPLITIT_INSTALLMENT_COUNT') || !Configuration::get('SPLITIT_INSTALLMENT_PRICE_TEXT') || Configuration::get('SPLITIT_IS_ENABLED') == 0 || Configuration::get('SPLITIT_ENABLE_PRICE') == 0 || Configuration::get('SPLITIT_INSTALLMENT_PRICE_ON_PAGES') == '' || Configuration::get('SPLITIT_INSTALLMENT_COUNT') == '' || Configuration::get('SPLITIT_INSTALLMENT_PRICE_TEXT') == '') {
            return;
        }

        $installment_price_on_pages = explode(',', Configuration::get('SPLITIT_INSTALLMENT_PRICE_ON_PAGES'));
        if (!in_array($page, $installment_price_on_pages)) {
            return;
        }

        $num_installments = Configuration::get('SPLITIT_INSTALLMENT_COUNT');
        $text = Configuration::get('SPLITIT_INSTALLMENT_PRICE_TEXT');

        $currency = Currency::getCurrency((int) $this->context->cart->id_currency);

        $installment = Tools::displayPrice(round($price / $num_installments, 2), $currency);

        $output = $installment . ' x ' . $num_installments . ' ' . $text;

        return $output;
    }

    public function isCartEmpty() {
        if ($this->context->cart->getOrderTotal() <= 0 || $this->context->cart->id == '') {
            return true;
        }

        return false;
    }

    public function getHelpLinkTitle(){
        return $this->l(Configuration::get('SPLITIT_HELP_LINK_TITLE'));
    }

    public function getHelpLinkURL(){
        return $this->l(Configuration::get('SPLITIT_HELP_LINK_URL'));
    }

}
