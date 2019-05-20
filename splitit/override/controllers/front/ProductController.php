<?php
/**
 * @author Splitit
 * @copyright 2017-2018 Splitit
 * @license BSD 2 License
 * @since 1.6.0
 */

class ProductController extends ProductControllerCore
{
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(
            'HOOK_SPLITIT_INSTALLMENT',
            Hook::exec('displaySplititInstallment', array('product' => $this->product))
        );
    }
}
