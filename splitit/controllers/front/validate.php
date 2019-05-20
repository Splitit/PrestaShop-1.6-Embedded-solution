<?php
/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @license BSD 2 License
 * @since 1.6.0
 */

class SplititValidateModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    
    public function __construct()
    {
        $this->auth = false;
        parent::__construct();
        $this->context = Context::getContext();
        include_once($this->module->getLocalPath().'splitit.php');
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        if (Tools::getValue('process') == 'validation') {
            $this->validation();
        }
        /*else if (Tools::getValue('process') == 'webhook')
            $this->webhook();*/
    }

    public function validation()
    {
        $splitit = new Splitit();
        if ($splitit->active) {
            $splitit->processPayment();
        } else {
            $this->context->cookie->__set("splitit_error", 'There was a problem with your payment');
            $controller = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc.php' : 'order.php';
            $location = $this->context->link->getPageLink($controller).(strpos($controller, '?') !== false ? '&' : '?').'step=3#splitit_error';
            Tools::redirect($location);
            //header('Location: '.$location);
        }
    }
}
