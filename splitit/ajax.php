<?php
/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @license BSD 2 License
 * @since 1.6.0
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/splitit.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

$context = Context::getContext();

$splitit = new Splitit();

if ($splitit->active && Tools::getIsset('action') && Tools::getValue('action') != '') {
    switch (Tools::getValue('action')) {
        case 'login':
            $reqInit = (!empty(Tools::getValue('reqInit'))) ? Tools::getValue('reqInit') : true;
            $result = $splitit->login($reqInit);
            break;
        case 'installmentPlans':
            $result = $splitit->getInstallmentPlans();
            break;
        case 'confirm':
            $result = $splitit->processPayment();
            break;
    }

    die(Tools::jsonEncode($result));
}
