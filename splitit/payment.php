<?php
/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @license BSD 2 License
 * @since 1.6.0
 */

/* SSL Management */
$useSSL = true;

require('../../config/config.inc.php');
Tools::displayFileAsDeprecated();

// init front controller in order to use Tools::redirect
$controller = new FrontController();
$controller->init();

Tools::redirect(Context::getContext()->link->getModuleLink('splitit', 'payment'));
