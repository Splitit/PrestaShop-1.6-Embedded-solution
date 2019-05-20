<?php
/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @license BSD 2 License
 * @since 1.6.0
 */

class SplititPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        include_once($this->module->getLocalPath().'splitit.php');
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $splitit = new Splitit();

        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart)
            || $cart->id == ''
            || ($cart->getOrderTotal() <= 0)
            || !$splitit->active || !Configuration::get('SPLITIT_IS_ENABLED')
            || !Configuration::get('SPLITIT_API_KEY')
            || !Configuration::get('SPLITIT_API_USER_NAME')
            || !Configuration::get('SPLITIT_API_PASSWORD')) {
            Tools::redirect('index.php?controller=order');
        }


        // Get saved credit cards types from configuration
        $saved_credit_cards = explode(',', Configuration::get('SPLITIT_CARD_TYPES'));

        $currency = Currency::getCurrency((int)$this->context->cart->id_currency);
        $cart_total = $this->context->cart->getOrderTotal();
        // Installment Setup
        if (Configuration::get('SPLITIT_INSTALLMENT_SETUP') == IS_FIXED || Configuration::get('SPLITIT_INSTALLMENT_SETUP' == IS_DEPENDING_ON_CART)) {
            $fixed_installment = explode(',', Configuration::get('SPLITIT_FIXED_INSTALLMENT'));
            $front_drop = array();
            foreach ($fixed_installment as $installment) {
                $front_drop[$installment] = $installment. ' installments of '. Tools::displayPrice(round($cart_total / $installment, 2), $currency);
            }
        }

        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'isoCode' => $this->context->language->iso_code,
            'this_path' => $this->module->getPathUri(),
            'this_path_splitit' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
            'splitit_ps_version' => _PS_VERSION_,
            'help_link_title' => $splitit->getHelpLinkTitle(),
            'help_link_url' => $splitit->getHelpLinkURL(),
            'credit_cards' => SplitIt::getCreditCards(),
            'saved_credit_cards' => $saved_credit_cards,
            'months' => Splitit::getMonths(),
            'years' => Splitit::getYears(),
            'installments' => $front_drop,
            'path' => $this->module->getPathUri(),
        ));

        //$this->context->controller->addJS($this->module->getPathUri().'js/splitit.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/jquery.payment.min.js');
        $this->context->controller->addJS($this->module->getPathUri().'views/js/splitit.js');
        $this->context->controller->addCSS($this->module->getPathUri().'views/css/splitit.css');

        $this->setTemplate('payment_execution.tpl');
    }
}
