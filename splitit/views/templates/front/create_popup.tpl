{*
*  @author Splitit
*  @copyright  2017-2018 Splitit
*  @since 1.6.0
*  @license BSD 2 License
*}

<div class="approval-popup_ovelay" style=""></div>
<div id="approval-popup" style="">
    <div id="main">
        <div class="_popup_overlay"></div>
        <!-- Start small inner popup -->

        <!-- Start Term and Condition Popup -->
        <div id="termAndConditionpopup" style=" ">
            <div class="popup-block">
                <div class="popup-content" style="">
                    <!-- start close button on terms-condition popup -->
                    <div class="popup-footer" style="">
                        <div id="payment-schedule-close-btn" class="popup-btn"  style="">
                            <div class="popup-btn-area-terms" style="">
                                <span id="termAndConditionpopupCloseBtn" class="popup-btn-icon-terms" style="">
                                    <img style="width:25px;" src="{$module_dir|escape:'htmlall':'UTF-8'}splitit/views/img/approval-popup-close.png">
                                </span>
                            </div>
                        </div>
                    </div>
                     <!-- end close button on terms-condition popup -->
                    <p style="text-align: left;">{l s='1. Buyer, whose name appears below ("Buyer", "You", or "Your"), promises to pay the full amount of the Total Authorized Purchase Price in the number of installment payments set forth in the Recurring Installment Payment Authorization ("Authorization") to Seller ("Seller", "We" or "Us") by authorizing Seller to charge Buyer’s credit card in equal monthly installments as set forth in the Authorization (each an "Installment") each month until paid in full.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='2. Buyer agrees that Seller will obtain authorization on Buyer’s credit card for the full amount of the Purchase at the time of sale, and Seller will obtain authorizations on Buyer’s credit card each month for the Installment and the entire remaining balance of the Purchase. Buyer understands that this authorization will remain in effect until Buyer cancels it in writing.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='3. Buyer acknowledges that Seller obtaining initial authorization for the Purchase, along with monthly authorization for each Installment and the outstanding balance, may adversely impact Buyer’s available credit on Buyer’s credit card. Buyer agrees to hold Seller harmless for any adverse consequences to Buyer.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='4.Buyer agrees to notify Seller in writing via Buyer’s user account at' mod='splitit'} <a title="consumer.Splitit.com" href="http://consumer.Splitit.com" target="_blank">consumer.splitit.com</a> {l s='of any changes to Buyer’s credit card account information or termination of this authorization. We will update such information and process such requests within 30 days after our receipt of such request. Buyer understands that the Installment payments may be authorized and charged on the next business day. Buyer further understands that because these are electronic transactions, any authorizations and charges may be posted to Your account as soon as the Installment payment dates.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='5. Any Installment amounts due under this contract that have been charged to Buyer’s credit card and not paid when due, pursuant to Your agreement with Your credit card issuer ("Issuer"), will be charged interest at the Annual Percentage Rate stated in Your Issuer’s Federal Truth-in-Lending Disclosure statement until the Installments are fully paid. So long as You timely pay each Installment to Your Issuer when due, Issuer will not charge Buyer interest on such Installment. Issuer may charge Buyer interest on any other balance You may have on Your credit card in excess of the Installment amount.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='6. In the case of an authorization being rejected for any reason, Buyer understands that Seller may, in its discretion, attempt to process the charge again within seven (7) days.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='7. In the event that Buyer’s Issuer fails to pay an Installment for any reason, Seller, at its discretion, may charge Buyer’s credit card at any time for the full outstanding amount due.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='8. In consideration for services provided by Splitit USA, Inc. ("Splitit") to Seller, Buyer agrees that Splitit will have the right to communicate with and solicit Buyer via e-mail (or other means). This provision is operational for not less than five (5) years from the date of the initial authorization.' mod='splitit'}<br>
                    {l s='9. Buyer understands that Splitit is not a party to this Agreement, which is solely between Buyer and Seller.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='10. Buyer understands and agrees that Splitit is not responsible for the delivery and quality of goods purchased in this transaction.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='11. Buyer acknowledges that the origination of any authorized transactions to the Buyer’s account must comply with the provisions of U.S. law. Buyer certifies that Buyer is an authorized user of the credit card utilized for this transaction and the Installments and will not dispute these transactions with Buyer’s credit card company, so long as the authorizations correspond to the terms indicated in the authorization form.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='12. Buyer agrees that if delivery of the goods or services are not made at the time of execution of this contract, the description of the goods or services and the due date of the first Installment may be inserted by Seller in Seller’s counterpart of the contract after it has been signed by Buyer.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='13. If any provision of this contract is determined to be invalid, it shall not affect the remaining provisions hereof.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='14. PRIVACY POLICY. Buyer’s privacy is important to us. You may obtain a copy of Splitit’s Privacy Policy by visiting their website at' mod='splitit'} <a title="consumer.Splitit.com" href="http://consumer.Splitit.com" target="_blank">consumer.splitit.com</a>. {l s='As permitted by law, Seller and Splitit may share information about our transactions and experiences with Buyer with other affiliated companies and unaffiliated third parties, including consumer reporting agencies and other creditors. However, except as permitted by law, neither Seller nor Splitit may share information which was obtained from credit applications, consumer reports, and any third parties with companies affiliated with us if Buyer instructs us not to share this information. If Buyer does not want us to share this information, Buyer shall notify us in writing via Buyer’s user account at' mod='splitit'} <a title="consumer.Splitit.com" href="http://consumer.Splitit.com" target="_blank">consumer.splitit.com</a> {l s='using the password Buyer was provided with for such notification and for accessing information on Splitit’s website. Buyer shall include Buyer’s name, address, account number and the last four digits of Buyer’s credit card number used in this transaction so such request can be honored. Seller may report about Your account to consumer reporting agencies. Late payments, missed payments, or other defaults on Your credit card account may be reflected by Your Issuer in Your credit report.' mod='splitit'}</p>
                    <p style="text-align: left;">{l s='15. ARBITRATION. Any claim, dispute or controversy ("Claim") arising from or connected with this Agreement, including the enforceability, validity or scope of this arbitration clause or this Agreement, shall be governed by this provision. Upon the election of Buyer or Seller by written notice to the other party, any Claim shall be resolved by arbitration before a single arbitrator, on an individual basis, without resort to any form of class action ("Class Action Waiver"), pursuant to this arbitration provision and the applicable rules of the American Arbitration Association ("AAA") in effect at the time the Claim is filed. Any arbitration hearing shall take place within the State of New York, County of New York. At the written request of Buyer, any filing and administrative fees charged or assessed by the AAA which are required to be paid by Buyer and that are in excess of any filing fee Buyer would have been required to pay to file a Claim in state court in New York shall be advanced and paid for by Seller. The arbitrator may not award punitive or exemplary damages against any party. IF ANY PARTY COMMENCES ARBITRATION WITH RESPECT TO A CLAIM, NEITHER BUYER OR SELLER WILL HAVE THE RIGHT TO LITIGATE THAT CLAIM IN COURT OR HAVE A JURY TRIAL ON THAT CLAIM, OR TO ENGAGE IN PRE-ARBITRATION DISCOVERY, EXCEPT AS PROVIDED FOR IN THE APPLICABLE ARBITRATION RULES. FURTHER, BUYER WILL NOT HAVE THE RIGHT TO PARTICIPATE AS A REPRESENTATIVE OR MEMBER OF ANY CLASS OF CLAIMANTS PERTAINING TO THAT CLAIM, AND BUYER WILL HAVE ONLY THOSE RIGHTS THAT ARE AVAILABLE IN AN INDIVIDUAL ARBITRATION. THE ARBITRATOR’S DECISION WILL BE FINAL AND BINDING ON ALL PARTIES, EXCEPT AS PROVIDED IN THE FEDERAL ARBITRATION ACT ("the FAA"). This Arbitration Provision shall be governed by the FAA, and, if and where applicable, the internal laws of the State of New York. If any portion of this Arbitration provision is deemed invalid or unenforceable, it shall not invalidate the remaining portions of this Arbitration provision or the Agreement, provided however, if the Class Action Waiver is deemed invalid or unenforceable, then this entire Arbitration provision shall be null and void and of no force or effect, but the remaining terms of this Agreement shall remain in full force and effect. Any appropriate court having jurisdiction may enter judgment on any award.' mod='splitit'}</p>
                </div>
            </div>
        </div>
      
        <!-- // Close Term and Condition Popup -->
        <div id="payment-schedule" style=" ">
            <div class="popup-block">
                <div class="popup-content" style="">
                    <table class="popupContentTable" style="">
                        <thead>
                            <tr>
                                <th style="width: 1em;"></th>
                                <th style="text-align:center;">{$scheduleChargedDateText|escape:'htmlall':'UTF-8'}</th>
                                <th style="text-align:center;">{$scheduleChargedAmountText|escape:'htmlall':'UTF-8'}</th>
                                <th style="text-align:center;">{$scheduleRequiredAvailableCreditText|escape:'htmlall':'UTF-8'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if isset($scheduleItems)}
                                {foreach from=$scheduleItems key=scheduleItem_key item=scheduleItem} 
                                    <tr>
                                        <td style="text-align: left;">{$scheduleItem.InstallmentNumber|escape:'htmlall':'UTF-8'}</td>
                                        <td>{$scheduleItem.DateOfCharge|escape:'htmlall':'UTF-8'|date_format:"m/d/Y"}</td>
                                        <td>{$currencySymbol|escape:'htmlall':'UTF-8'}{$scheduleItem.ChargeAmount|escape:'htmlall':'UTF-8'}</td>
                                        <td>{$currencySymbol|escape:'htmlall':'UTF-8'}{$scheduleItem.RequiredAvailableCredit|escape:'htmlall':'UTF-8'}</td>
                                    </tr>
                                {/foreach}
                            {/if}
                        </tbody>
                    </table>
                </div>
                <div class="popup-footer" style="">
                    <div id="payment-schedule-close-btn" class="popup-btn"  style="">
                        <div class="popup-btn-area" style="">
                            <span id="complete-payment-schedule-close" class="popup-btn-icon" style="">{l s='Close' mod='splitit'}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End small inner popup -->


        <div class="mainHeader">
            <span class="closeapprovalpopup_btn" style="" onclick="closeApprovalPopup();">
                <img style="width:100%;" src="{$module_dir|escape:'htmlall':'UTF-8'}splitit/views/img/approval-popup-close.png">
            </span>
            <table id="wiz-header" width="100%;">
                <tbody>
                    <tr>
                        <td class="wiz-header-side wiz-header-left" style=""></td>
                        <td class="wiz-header-center" style="">
                            <div>{l s='TOTAL PURCHASE' mod='splitit'}:</div>
                            <div class="currencySymbolIcon" style="">{$currencySymbol|escape:'htmlall':'UTF-8'}{$totalAmount|escape:'htmlall':'UTF-8'}</div>
                        </td>
                        <td class="wiz-header-side wiz-header-right" style=""></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin-top: auto;">
     
            <div class="form-block" style="">
                <div class="form-block-area" style="">
                    <div class="spacer15" style=""></div>
                    <div class="tableResponsive">
                        <table class="tablePage2" style="" cellspacing="0" cellpadding="0">
                            <tbody>
                            {if isset($planDataSection)}
                              <tr class="tablePage2TD"  style="">
                              <td>{$planDataSection.NumberOfInstallments.Text|escape:'htmlall':'UTF-8'}</td>
                              <td class="text-right" style="">
                              <span>{$planDataSection.NumberOfInstallments.NumOfInstallments|escape:'htmlall':'UTF-8'}</span>
                              </td></tr>

                              <tr class="tablePage2TD" style="">
                              <td>{$planDataSection.FirstInstallmentAmount.Text|escape:'htmlall':'UTF-8'}</td>
                              <td class="text-right" style="">
                              <span>{$currencySymbol|escape:'htmlall':'UTF-8'}{$planDataSection.FirstInstallmentAmount.Amount|escape:'htmlall':'UTF-8'}</span>
                              </td></tr>

                              <tr class="tablePage2TD" style="">
                              <td>{$planDataSection.SubsequentInstallmentAmount.Text|escape:'htmlall':'UTF-8'}</td>
                              <td class="text-right">
                              <span>{$currencySymbol|escape:'htmlall':'UTF-8'}{$planDataSection.SubsequentInstallmentAmount.Amount|escape:'htmlall':'UTF-8'}</span>
                              </td></tr>

                              <tr class="tablePage2TD" style="">
                              <td>{$planDataSection.RequiredAvailableCredit.Text|escape:'htmlall':'UTF-8'}</td>
                              <td class="text-right" style="">
                              <span>{$currencySymbol|escape:'htmlall':'UTF-8'}{$planDataSection.RequiredAvailableCredit.Amount|escape:'htmlall':'UTF-8'}</span>
                              </td></tr>
                            {/if}
                            </tbody>
                        </table>
                    </div>
                    <a id="payment-schedule-link" style="">{l s='See Complete Payment Schedule' mod='splitit'}</a>
                </div>
            </div>
            <div class="form-block right" style="">
                <div class="form-block-area">
                    <div>
                        <div class="important_note_sec" style="">{$importantNotesHeader|escape:'htmlall':'UTF-8'}:</div>
                        <div class="pnlEula" style="">{$importantNotesBody|escape:'htmlall':'UTF-8'}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="termAndConditionBtn" style=""> 
            <a href="{$termsConditionsLink|escape:'htmlall':'UTF-8'}" target="_blank" style="">{$termsConditionsText|escape:'htmlall':'UTF-8'}</a> | <a href="{$servicesLink|escape:'htmlall':'UTF-8'}" style="" target="_blank">{$servicesText|escape:'htmlall':'UTF-8'}</a>
        </div>
      
    </div>
</div>    