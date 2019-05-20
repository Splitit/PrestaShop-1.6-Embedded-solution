{*
*  @author Splitit
*  @copyright  2017-2018 Splitit
*  @since 1.6.0
*  @license BSD 2 License
*}
 
{if isset($splitit_save_success)}
<div class="alert alert-success" role="alert">
    <strong>{l s='Congratulation !' mod='splitit'}</strong>        
    {if $sandbox_mode == 0}
    {l s='You can now start accepting Payment with Splitit.' mod='splitit'}
    {elseif $sandbox_mode == 1}
    {l s='You can now start testing Splitit. Don\'t forget to comeback to this page and activate the live mode in order to start accepting payements.' mod='splitit'}
    {/if}
</div>
{/if}
<div class="container">

    <div class="row">
        <h3>{l s='Splitit Payment Settings' mod='splitit'}</h3>    

        <div class="col-sm-12">
            <div class="comment">
                <a href="{$split_url|escape:'htmlall':'UTF-8'}" target="_blank">
                    {l s='Click here to sign up for a Splitit account' mod='splitit'}
                </a>
            </div>
            <form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" id="splitit_configuration">
                <table cellspacing="0" class="table">
                    <tbody>
                        <tr>
                            <td>
                                <label for="is_enabled"> {l s='Enabled' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="is_enabled" name="is_enabled">
                                    <option value="1" {if $is_enabled == 1}selected="selected"{/if}>{l s='Yes' mod='splitit'}</option>
                                    <option value="0" {if $is_enabled == 0}selected="selected"{/if}>{l s='No' mod='splitit'}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><h4 class="splitit_label_header"> {l s='General settings' mod='splitit'}</h4></td>
                        </tr>
                        <tr>
                            <td>
                                <label> {l s='Terminal API key' mod='splitit'}</label>
                            </td>
                            <td>
                                <input id="api_key" name="api_key" value="{$api_key|escape:'htmlall':'UTF-8'}" class=" input-text" type="text" >
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label> {l s='API Username' mod='splitit'}</label>
                            </td>
                            <td>
                                <input id="api_user_name" name="api_user_name" value="{$api_user_name|escape:'htmlall':'UTF-8'}" class=" input-text" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label> {l s='API Password' mod='splitit'}</label>
                            </td>
                            <td>
                                <input id="api_password" name="api_password" value="{$api_password|escape:'htmlall':'UTF-8'}" class=" input-text" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label> {l s='Sandbox Mode' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="sandbox_mode" name="sandbox_mode">
                                    <option value="1" {if $sandbox_mode == 1}selected="selected"{/if}>{l s='Yes' mod='splitit'}</option>
                                    <option value="0" {if $sandbox_mode == 0}selected="selected"{/if}>{l s='No' mod='splitit'}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label> {l s='Check Credential API' mod='splitit'}</label>
                            </td>
                            <td>
                                <button id="splitit_button" title="Check Settings" type="button" class="btn btn-primary" onClick="login('{$baseUrl|escape:'htmlall':'UTF-8'}');"><span>{l s='Check Settings' mod='splitit'}</span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4 class="splitit_label_header"> {l s='Shop Setup' mod='splitit'}</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label> {l s='Credit Card Types' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="card_types" name="card_types[]" size="10" multiple="multiple">
                                    {foreach from=$credit_cards key=card_key item=credit_card}
                                        <option value="{$card_key|escape:'htmlall':'UTF-8'}" {if $card_key|in_array:$card_types}selected="selected"{/if}>{$credit_card|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}                                
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4 class="splitit_label_header"> {l s='Installment Setup' mod='splitit'}</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="first_payment"> {l s='First Payment' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="first_payment" name="first_payment">
                                    {foreach from=$first_payemnt_options key=first_payemnt_option_key item=first_payemnt_option}
                                        <option value="{$first_payemnt_option_key|escape:'htmlall':'UTF-8'}" {if $first_payemnt_option_key == $first_payment}selected="selected"{/if}>{$first_payemnt_option|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr class="percentage_of_order">
                            <td>
                                <label for="percentage_of_order"> {l s='Percentage of order %' mod='splitit'}</label>
                            </td>
                            <td>
                                <input id="percentage_of_order" name="percentage_of_order" value="{$percentage_of_order|escape:'htmlall':'UTF-8'}" class=" input-text" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="installment_setup"> {l s='Select installment setup' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="installment_setup" name="installment_setup">
                                    {foreach from=$installmentSetups key=setup_key item=installmentSetup}
                                        <option value="{$setup_key|escape:'htmlall':'UTF-8'}" {if $setup_key == $installment_setup}selected="selected"{/if}>{$installmentSetup|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="fixed_installment"> {l s='Fixed' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="fixed_installment" name="fixed_installment[]" size="10" multiple="multiple">
                                    {foreach from=$installments key=installment_key item=installment}
                                        <option value="{$installment_key|escape:'htmlall':'UTF-8'}" {if $installment_key|in_array:$fixed_installment}selected="selected"{/if}>{$installment|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                                <div class="tiers_table_overlay2" style="display: none;"></div>
                            </td>
                        </tr>       

                        <tr>
                            <td colspan="2">
                                <h4 class="splitit_label_header"> {l s='Installment price setup' mod='splitit'}</h4></td>
                        </tr>
                        <tr>
                            <td>
                                <label> {l s='Enable Installment Price' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="enable_price" name="enable_price">
                                    <option value="1" {if $enable_price == 1}selected="selected"{/if}>{l s='Yes' mod='splitit'}</option>
                                    <option value="0" {if $enable_price == 0}selected="selected"{/if}>{l s='No' mod='splitit'}</option>
                                </select>
                            </td>

                            <script type="text/javascript">

                            </script>
                        </tr>
                        <tr class="instalment_price_row">
                            <td>
                                <label> {l s='Display Installment Price on pages' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="installment_price_on_pages" name="installment_price_on_pages[]" size="10" multiple="multiple">
                                    {foreach from=$show_on_pages key=page_key item=show_on_page}
                                        <option value="{$page_key|escape:'htmlall':'UTF-8'}" {if $page_key|in_array:$installment_price_on_pages}selected="selected"{/if}>{$show_on_page|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr class="instalment_price_row">
                            <td>
                                <label> {l s='Number of installments for display' mod='splitit'}</label>
                            </td>
                            <td>
                                <select id="installemnt_count" name="installemnt_count">
                                    {foreach from=$installments key=installment_key item=installment}
                                        <option value="{$installment_key|escape:'htmlall':'UTF-8'}" {if $installment_key == $installemnt_count}selected="selected"{/if}>{$installment|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr class="instalment_price_row">
                            <td>
                                <label> {l s='Installment price text' mod='splitit'}</label>
                            </td>
                            <td>
                                <input id="price_text" name="price_text" value="{$price_text|escape:'htmlall':'UTF-8'}" class="form-control" type="text">
                            </td>
                        </tr>                                         
                        <tr>
                            <td colspan="2"><input type="submit" name="submitSplitit" value="{l s='Save' mod='splitit'}" id="splitit_submit" class="btn btn-primary pull-right" /></td>
                        </tr>

                    </tbody>
                </table>
            </form>    
        </div>
    </div>

</div>


