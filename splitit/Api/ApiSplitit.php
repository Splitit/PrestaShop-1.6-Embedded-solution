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

class ApiSplitit
{
    protected $_sessionId = null;
    protected $_gwUrl = null;


    /*     * ********************************************************* */
    /*     * ******************** CONNECT METHODS ******************** */
    /*     * ********************************************************* */

    public function __construct()
    {
        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
            return array(
                'status' => false,
                'code'   => '2',
                'message' => "Unable to process order this time."
            );
        }
    }

    /**
     * @param $gwUrl
     * @param $params
     *
     * @return array
     */
    public function login($gwUrl, $params)
    {
        $result =  $this->doCurl($gwUrl, Tools::ucfirst(__FUNCTION__), $params);
        $result = Tools::jsonDecode($result, true);

        if (class_exists('Logger')) {
            Logger::addLog('Splitit Login API Call Response <br/>'.json_encode($result), 1, null, 'Login API', (int)$this->context->cart->id, true);
        }

        if ($result) {
            $this->_sessionId = (isset($result['SessionId']) && $result['SessionId'] != '') ? $result['SessionId'] : null;

            if (is_null($this->_sessionId)) {
                if (isset($result["serverError"])) {
                    $response = array(
                        'status' => false,
                        'code'   => '2',
                        'message' => $this->getServerDownMsg()
                    );
                } else {
                    $gatewayErrorCode = $result["ResponseHeader"]["Errors"][0]["ErrorCode"];
                    $gatewayErrorMsg = $result["ResponseHeader"]["Errors"][0]["Message"];

                    $response = array(
                        'status' => false,
                        'code'   => $gatewayErrorCode,
                        'message' => $gatewayErrorMsg
                    );
                }
            } else {
                // Call Initate API
                $response = array(
                    'status' => true,
                    'code'   => 1,
                    'message' => 'Login Successfully'
                );
            }

            $this->_gwUrl = $gwUrl;
            //$this->_apiTerminalKey = $params['ApiKey'];

            // set Splitit session id into session
            $this->context->cookie->__set('sessionId', $this->_sessionId);
        }

        return $response;
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return (!is_null($this->_sessionId));
    }


    /**
     * @param $apiUrl
     *
     * @return array
     */

    public function installmentPlanInit($apiUrl)
    {
        $response = array(
            "status" => false,
            "code" => 2,
            "message" => ""
        );

        try {
            $billAddress = new Address($this->context->cart->id_address_invoice);
            $state_name = State::getNameById($billAddress->id_state);

            $params = array(
                "RequestHeader" => array(
                    "SessionId" => $this->context->cookie->sessionId,
                    "ApiKey"    => Configuration::get('SPLITIT_API_KEY'),
                ),
                "PlanData"      => array(
                    "Amount"    => array(
                        "Value" => round($this->context->cart->getOrderTotal(), 2),
                        "CurrencyCode" => $this->context->currency->iso_code,
                    ),
                    "RefOrderNumber" => "",
                    "PurchaseMethod" => "ECommerce",
                    "AutoCapture" => "true",
                ),
                "BillingAddress" => array(
                    "AddressLine" => $billAddress->address1,
                    "AddressLine2" => $billAddress->address2,
                    "City" => $billAddress->city,
                    "State" => $state_name,
                    "Country" => $billAddress->country,
                    "Zip" => $billAddress->postcode,
                ),
                "ConsumerData" => array(
                    "FullName" => $this->context->cookie->customer_firstname." ".$this->context->cookie->customer_lastname,
                    "Email" => $this->context->cookie->email,
                    "PhoneNumber" => ($billAddress->phone) ? $billAddress->phone : $billAddress->phone_mobile,
                    "CultureName" => $this->context->language->language_code
                )
            );

            $cartProducts = Context::getContext()->cart->getProducts(true);
            if (!empty($cartProducts) && count($cartProducts) > 0) {
                $data = array();
                foreach ($cartProducts as $product) {
                    $data[] = array(
                        "Name" => $product["name"],
                        "SKU" => $product["id_product"],
                        "Price" => array(
                        "Value" => $product["price"],
                        "CurrencyCode" => $this->context->currency->iso_code
                    ),
                        "Quantity" => $product["cart_quantity"],
                        "Description" => strip_tags($product["description_short"])
                    );
                }

                $params["CartData"] = array(
                    "Items" => $data,
                    "AmountDetails" => array(
                    "Subtotal" => 0,
                    "Tax" => 0,
                    "Shipping" => 0
                )
                );
            }

            /*            print_r(json_encode($params));
            die();*/

            // check if cunsumer dont filled data
            if ($billAddress->address1 == ""
               || $billAddress->city == ""
               || $billAddress->postcode == ""
               || $this->context->cookie->customer_firstname == ""
               || $this->context->cookie->customer_lastname == ""
               || $this->context->cookie->email == ""
               || ($billAddress->phone == "" && $billAddress->phone_mobile == "")
              ) {
                $response["message"] = "Please fill required fields.";
                return $response;
            }


            $result = $this->doCurl($apiUrl, "InstallmentPlan/Initiate", $params);
            $result = Tools::jsonDecode($result, true);

            if (class_exists('Logger')) {
                Logger::addLog('Splitit Initiate Request for: '.$this->context->cookie->email.' <br/>'.json_encode($params), 1, null, 'Installment Plan Init', (int)$this->context->cart->id, true);

                Logger::addLog('Splitit InstallmentPlan/Initiate Response for: '.$this->context->cookie->email.' <br/>'.json_encode($result), 1, null, 'Installment Plan Init', (int)$this->context->cart->id, true);
            }

            if (isset($result) && isset($result["InstallmentPlanInfoUrl"]) && $result["InstallmentPlanInfoUrl"] != "") {
                if (class_exists('Logger')) {
                    Logger::addLog('Splitit Initiate Request for: '.$result["InstallmentPlan"]["InstallmentPlanNumber"].' <br/>'.json_encode($params), 1, null, 'Installment Plan Init', (int)$this->context->cart->id, true);

                    Logger::addLog('Splitit InstallmentPlan/Initiate Response for: '.$result["InstallmentPlan"]["InstallmentPlanNumber"].' <br/>'.json_encode($result), 1, null, 'Installment Plan Init', (int)$this->context->cart->id, true);
                }

                // Set Installment plan number and url into session
                $this->context->cookie->__set('installmentPlanNumber', $result["InstallmentPlan"]["InstallmentPlanNumber"]);
                $this->context->cookie->__set('installmentPlanInfoUrl', $result["InstallmentPlanInfoUrl"]);

                $response["message"] = "Logged in and intitated";
                $response['code'] = 1;
                $response['status'] = true;
            } elseif (isset($result["ResponseHeader"]) && count($result["ResponseHeader"]["Errors"])) {
                $errorMsg = "";
                $i = 1;
                foreach ($result["ResponseHeader"]["Errors"] as $value) {
                    $errorMsg .= "Code : ".$value["ErrorCode"]." - ".$value["Message"];
                    if ($i < count($result["ResponseHeader"]["Errors"])) {
                        $errorMsg .= ", ";
                    }
                    $i++;
                }

                $response["message"] = $errorMsg;
            } elseif (isset($result["serverError"])) {
                $response["message"] = $result["serverError"];
            }
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @param $gwUrl
     *
     * @return array
     */

    public function getInstallmentPlans($url)
    {
        $response = array(
            "status" => false,
            "code" => 2,
            "message" => ""
        );

        $installmentPlanInfoUrlResponse = $this->doCurl($url);
        $resultDecoded = Tools::jsonDecode($installmentPlanInfoUrlResponse, true);

        if (isset($this->context->cookie->installmentPlanNumber)) {
            if (class_exists('Logger')) {
                Logger::addLog('Splitit Get Installment Plans Response for: '.$this->context->cookie->installmentPlanNumber.' <br/>'.json_encode($installmentPlanInfoUrlResponse), 1, null, 'Get Installment Plan', (int)$this->context->cart->id, true);
            }
        } else {
            Logger::addLog('Splitit Get Installment Plans Response <br/>'.json_encode($installmentPlanInfoUrlResponse), 1, null, 'Get Installment Plan', (int)$this->context->cart->id, true);
        }

        if (isset($resultDecoded["Global"]["ResponseResult"]["Errors"]) && count($resultDecoded["Global"]["ResponseResult"]["Errors"])) {
            $i = 1;
            $errorMsg = "";
            foreach ($resultDecoded["Global"]["ResponseResult"]["Errors"] as $value) {
                $errorMsg .= "Code : ".$value["ErrorCode"]." - ".$value["Message"];
                if ($i < count($resultDecoded["Global"]["ResponseResult"]["Errors"])) {
                    $errorMsg .= ", ";
                }

                if ($value["Message"] == 'Session expired' || $value["ErrorCode"] == 704) {
                    $this->context->cookie->__set('session_expired', true);
                }

                $i++;
            }
            $response["message"] = $errorMsg;
        } elseif (isset($resultDecoded["serverError"])) {
            $response["message"] = $installmentPlanInfoUrlResponse["serverError"];
        } else {
            $popupHtml = $this->createPopupHtml($installmentPlanInfoUrlResponse);
            $response["status"] = true;
            $response["code"] = 1;
            $response["message"] = $popupHtml;
        }

        return $response;
    }


    /**
     * @param $gwUrl
     *
     * @return array
     */

    public function getInstallmentPlanInfo($gwUrl, $params)
    {
        $response = array(
            "status" => false,
            "code" => 2,
            "message" => ""
        );

        $installmentPlanInfoUrlResponse =  $this->doCurl($gwUrl, "InstallmentPlan/Get", $params);
        $resultDecoded = Tools::jsonDecode($installmentPlanInfoUrlResponse, true);

        /*        print_r($resultDecoded);
        die();*/

        if (isset($resultDecoded["ResponseHeader"]["Errors"]) && count($resultDecoded["ResponseHeader"]["Errors"])) {
            $i = 1;
            $errorMsg = "";
            foreach ($resultDecoded["ResponseHeader"]["Errors"] as $value) {
                $errorMsg .= "Code : ".$value["ErrorCode"]." - ".$value["Message"];
                if ($i < count($resultDecoded["ResponseHeader"]["Errors"])) {
                    $errorMsg .= ", ";
                }

                if ($value["Message"] == 'Session expired' || $value["ErrorCode"] == 704) {
                    $this->context->cookie->__set('session_expired', true);
                }

                $i++;
            }
            $response["message"] = $errorMsg;
        } elseif (isset($resultDecoded["serverError"])) {
            $response["message"] = $installmentPlanInfoUrlResponse["serverError"];
        } else {
            $response["status"] = true;
            $response["code"] = 1;
            $response["message"] = $resultDecoded["PlansList"][0]["OriginalAmount"]["Value"];
        }

        return $response;
    }


    /**
     * @param $gwUrl
     * @param $data = array()
     *
     * @return array
     */

    public function createInstallmentPlan($gwUrl, $data = array())
    {
        $response = array(
            "status" => false,
            "code" => 2,
            "message" => ""
        );

        $params = array(
            "RequestHeader" => array(
            "SessionId" => $this->context->cookie->sessionId,
            "ApiKey"    => Configuration::get('SPLITIT_API_KEY'),
        ),
            "InstallmentPlanNumber" => $this->context->cookie->installmentPlanNumber,
            "PlanData" => array(
            "NumberOfInstallments" => $data["num_installments"],
        ),
            "CreditCardDetails" => array(
            "CardCvv" => $data["cc_cvv"],
            "CardHolderFullName" => $data["cc_holder_name"],
            "CardNumber" => $data["cc_number"],
            "CardExpMonth" => $data["cc_exp_month"],
            "CardExpYear" => $data["cc_exp_year"],
        ),
            "PlanApprovalEvidence" => array(
            "AreTermsAndConditionsApproved" => "True"
        )
        );

        // To Be used for logging data
        /*        $params1 = [
            "RequestHeader" => [
                "SessionId" => $this->context->cookie->sessionId,
                "ApiKey"    => Configuration::get('SPLITIT_API_KEY'),
            ],
            "InstallmentPlanNumber" => $this->context->cookie->installmentPlanNumber,
            "PlanData" => [
                "NumberOfInstallments" => $data["num_installments"],
            ],
            "PlanApprovalEvidence" => [
                "AreTermsAndConditionsApproved" => "True"
            ],
        ];*/

        $result = $this->doCurl($gwUrl, "InstallmentPlan/Create", $params);
        $resultDecoded = Tools::jsonDecode($result, true);

        unset($params["CreditCardDetails"]);
        //print_r($params);

        if (isset($this->context->cookie->installmentPlanNumber)) {
            if (class_exists('Logger')) {
                Logger::addLog('Splitit Create Installment Plans Request for: '.$this->context->cookie->installmentPlanNumber.' <br/>'.json_encode($params), 1, null, 'Create Installment Plans', (int)$this->context->cart->id, true);

                Logger::addLog('Splitit Create Installment Plans Response: '.$this->context->cookie->installmentPlanNumber.' <br/>'.json_encode($result), 1, null, 'Create Installment Plans', (int)$this->context->cart->id, true);
            }
        } else {
            if (class_exists('Logger')) {
                Logger::addLog('Splitit Create Installment Plans Request <br/>'.json_encode($params), 1, null, 'Create Installment Plans', (int)$this->context->cart->id, true);

                Logger::addLog('Splitit Create Installment Plans Response <br/>'.json_encode($result), 1, null, 'Create Installment Plans', (int)$this->context->cart->id, true);
            }
        }

        // show error if there is any error from spliti it when click on place order
        if (isset($resultDecoded["ResponseHeader"]["Succeeded"]) && $resultDecoded["ResponseHeader"]["Succeeded"] == true) {
            $response["status"] = true;
            $response["code"] = 1;
            $response["message"] = "Installment Plan Created";
        } else {
            $errorMsg = "";
            if (isset($resultDecoded["serverError"])) {
                $response["message"] = $resultDecoded["serverError"];
            } else {
                foreach ($resultDecoded["ResponseHeader"]["Errors"] as $value) {
                    $errorMsg .= $value["ErrorCode"]." : ".$value["Message"];
                }

                $response["message"] = $errorMsg;
            }
        }


        return $response;
    }

    public function updateRefOrderNumber($gwUrl, $order)
    {
        $response = array("status"=>false, "code" => 2, "message" => "");

        $params = array(
            "RequestHeader" => array(
                "SessionId" => $this->context->cookie->sessionId,
            ),
            "InstallmentPlanNumber" => $this->context->cookie->installmentPlanNumber,
            "PlanData" => array(
                "ExtendedParams" => array(
                    "CreateAck" => "Received",
                ),
                "RefOrderNumber" => $order->reference,
            )
        );

        $result = $this->doCurl($gwUrl, 'InstallmentPlan/Update', $params);
        $decodedResult = Tools::jsonDecode($result, true);

        if (isset($this->context->cookie->installmentPlanNumber)) {
            if (class_exists('Logger')) {
                Logger::addLog('Splitit Update Reference Order Number Request for: '.$this->context->cookie->installmentPlanNumber.' <br/>'.json_encode($params), 1, null, 'Update Reference Order Number', (int)$this->context->cart->id, true);

                Logger::addLog('Splitit Update Reference Order Number Response for: '.$this->context->cookie->installmentPlanNumber.' <br/>'.json_encode($result), 1, null, 'Update Reference Order Number', (int)$this->context->cart->id, true);
            }
        } else {
            if (class_exists('Logger')) {
                Logger::addLog('Splitit Update Reference Order Number Request <br/>'.json_encode($params), 1, null, 'Update Reference Order Number', (int)$this->context->cart->id, true);

                Logger::addLog('Splitit Update Reference Order Number Request <br/>'.json_encode($result), 1, null, 'Update Reference Order Number', (int)$this->context->cart->id, true);
            }
        }

        if (isset($decodedResult["ResponseHeader"]["Succeeded"]) && $decodedResult["ResponseHeader"]["Succeeded"] == 1) {
            $response["status"] = true;
            $response["code"] = 1;
            $response["message"] = "Information Updated";
        } elseif (isset($decodedResult["ResponseHeader"]) && count($decodedResult["ResponseHeader"]["Errors"])) {
            $errorMsg = "";
            $i = 1;
            foreach ($decodedResult["ResponseHeader"]["Errors"] as $value) {
                $errorMsg .= "Code : ".$value["ErrorCode"]." - ".$value["Message"];
                if ($i < count($decodedResult["ResponseHeader"]["Errors"])) {
                    $errorMsg .= ", ";
                }
                $i++;
            }

            $response["message"] = $errorMsg;
        }

        return $response;
    }

    public function createPopupHtml($approvalUrlResponse)
    {

        //global $smarty;

        $html = '';
        $approvalUrlResponseArr = Tools::jsonDecode($approvalUrlResponse, true);
        if (!empty($approvalUrlResponseArr) && isset($approvalUrlResponseArr["Global"]["ResponseResult"]) && isset($approvalUrlResponseArr["Global"]["ResponseResult"]["Succeeded"]) && $approvalUrlResponseArr["Global"]["ResponseResult"]["Succeeded"] == 1) {
            $this->context->smarty->assign(array(
                'module_dir'     => _MODULE_DIR_,
                'currencySymbol'  => $approvalUrlResponseArr["Global"]["Currency"]["Symbol"],
                'totalAmount' => $approvalUrlResponseArr["HeaderSection"]["InstallmentPlanTotalAmount"]["Amount"],
                'totalText' => $approvalUrlResponseArr["HeaderSection"]["InstallmentPlanTotalAmount"]["Text"],
                'scheduleChargedDateText' => $approvalUrlResponseArr["ScheduledPaymentSection"]["ChargedDateText"],
                'scheduleChargedAmountText' => $approvalUrlResponseArr["ScheduledPaymentSection"]["ChargedAmountText"],
                'scheduleRequiredAvailableCreditText' => $approvalUrlResponseArr["ScheduledPaymentSection"]["RequiredAvailableCreditText"],
                'termsConditionsText' => $approvalUrlResponseArr["ImportantNotesSection"]["AcknowledgeLink"]["Text"],
                'termsConditionsLink' => $approvalUrlResponseArr["ImportantNotesSection"]["AcknowledgeLink"]["Link"],
                'servicesText' => $approvalUrlResponseArr["LinksSection"]["PrivacyPolicy"]["Text"],
                'servicesLink' => $approvalUrlResponseArr["LinksSection"]["PrivacyPolicy"]["Link"],
                'scheduleItems' => $approvalUrlResponseArr["ScheduledPaymentSection"]["ScheduleItems"],
                'planDataSection' => $approvalUrlResponseArr["PlanDataSection"],
                'importantNotesHeader' => $approvalUrlResponseArr["ImportantNotesSection"]["ImportantNotesHeader"]["Text"],
                'importantNotesBody' => $approvalUrlResponseArr["ImportantNotesSection"]["ImportantNotesBody"]["Text"]
            ));

            $html .= $this->context->smarty->fetch(dirname(dirname(__FILE__)).'/views/templates/front/create_popup.tpl');
        }

        return $html;
    }


    /**
     * @param $gwUrl
     * @param $method
     * @param $params = array()
     *
     * @return array
     */

    public function doCurl($gwUrl, $method = '', $params = array())
    {
        if (empty($method) || empty($params)) {
            $url = trim($gwUrl) . '&format=json';
            $req_type = 'GET';
        } else {
            $url = trim($gwUrl, '/') . '/api/' . $method . '?format=JSON';
            $req_type = 'POST';
        }

        $ch = curl_init($url);
        $jsonData = Tools::jsonEncode($params);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length:' . Tools::strlen($jsonData)
            )
        );
        $result = curl_exec($ch);

        /*$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);*/
        // check for curl error eg: splitit server down.
        if (curl_errno($ch)) {
            //echo 'Curl error: ' . curl_error($ch);
            curl_close($ch);
            $result["serverError"] = $this->getServerDownMsg();
            return $result = Tools::jsonEncode($result);
        }
        curl_close($ch);
        return $result;
    }


    /**
     *
     * @return string
     */

    public function getServerDownMsg()
    {
        return "Failed to connect to splitit payment server. Please retry again later.";
    }
}
