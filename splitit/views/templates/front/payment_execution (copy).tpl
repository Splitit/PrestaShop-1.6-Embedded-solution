{*
*  @author Splitit
*  @copyright  2017-2018 Splitit
*  @since 1.6.0
*  @license BSD 2 License
*}

{literal}
<script>
var baseUrl = "{/literal}{$base_dir|escape:'htmlall':'UTF-8'}{literal}";
</script>
{/literal}

{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='splitit'}">{l s='Checkout' mod='splitit'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Splitit Payment' mod='splitit'}
{/capture}

<h2>{l s='Order summary' mod='splitit'}</h2>


{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
<p class="warning">{l s='Your shopping cart is empty.' mod='splitit'}</p>
{else}

<h3>{l s='Splitit Payment' mod='splitit'}</h3>
<p>
{l s='You have chosen to pay by splitit.' mod='splitit'}
<br/><br />
{l s='Here is a short summary of your order:' mod='splitit'}
</p>
<p style="margin-top:20px;">
- {l s='The total amount of your order is' mod='splitit'}
<span id="amount" class="price">{displayPrice price=$total}</span>
{if $use_taxes == 1}
	{l s='(tax incl.)' mod='splitit'}
{/if}
</p>
<form name="splitit_form" id="splitit_form" action="{$link->getModuleLink('splitit', 'validation', [], true)|escape:'html'}" method="post">
    <div class="splitit-payment-errors"></div>
	<div class="payment_module {if $splitit_ps_version < '1.5'}splitit-payment-15{/if}{if $splitit_ps_version > '1.5'}splitit-payment-16{/if}">
        
        <label for="p_method_pis_cc">{l s='Monthly payments - 0% Interest' mod='splitit'} <img alt="" src="{$path|escape:'htmlall':'UTF-8'}views/img/secure-icon.png">
            <div class="payment-images" id="payment-img">
                <img src="{$path|escape:'htmlall':'UTF-8'}views/img/cc-visa.png">
                <img src="{$path|escape:'htmlall':'UTF-8'}views/img/cc-mastercard.png">
                <span class="splitit-login-loader" style="display: none;"><img src="{$path|escape:'htmlall':'UTF-8'}views/img/opc-ajax-loader.gif">{l s='Login to Splitit...' mod='splitit'}</span>
            </div>
        </label>

        <ul class="form-list" id="payment_form_splitit">
            <li>
              <div class="form-group">            
                <label for="splitit_cc_type" class="required">{l s='Credit Card Type' mod='splitit'}</label>
                <div class="input-box">
                    <select id="splitit_cc_type" name="cc_type" class="form-control">
                    {foreach from=$credit_cards key=credit_card_key item=credit_card}
                        {if $credit_card_key|in_array:$saved_credit_cards}
                            <option value="{$credit_card_key|escape:'htmlall':'UTF-8'}">{$credit_card|escape:'htmlall':'UTF-8'}</option>
                        {/if}    
                    {/foreach}                
                    </select>
                </div></div>
            </li>
            <li>
              <div class="form-group">
                <label for="splitit_cc_number" class="required">{l s='Credit Card Number' mod='splitit'}</label>
                <div class="input-box">
                    <input type="text" id="splitit_cc_number" name="cc_number" title="{l s='Credit Card Number' mod='splitit'}" class="form-control" />
                </div>
                </div>                
            </li>
            <li>
              <div class="form-group">            
                <label for="splitit_expiration" class="required">{l s='Expiration (MM/YYYY)' mod='splitit'}</label>
                <div class="input-box">
                    <div class="v-fix">
                        <select id="splitit_expiration_month" name="expiration_month" class="month validate-cc-exp required-entry form-control">
                        <option value>{l s='Month' mod='splitit'}</option>option>
                        {foreach from=$months key=month_key item=month}
                            <option value="{$month_key|escape:'htmlall':'UTF-8'}">{$month|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                        </select>
                    </div>
                    <div class="v-fix">
                        <select id="splitit_expiration_yr" name="expiration_yr" class="year required-entry form-control">
                        <option value>{l s='Year' mod='splitit'}</option>option>
                        {foreach from=$years key=year_key item=year}
                            <option value="{$year_key|escape:'htmlall':'UTF-8'}">{$year|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                        </select>
                    </div>
                </div>
            </li>
            <li class="cc_verification_number">
              <div class="form-group">                            
                <label for="splitit_cc_cid" class="required">{l s='Card Validation Code' mod='splitit'}</label>
                <div class="input-box">
                    <div class="v-fix">
                        <input type="text" title="{l s='Card Validation Code' mod='splitit'}" class="input-text cvv required-entry validate-cc-cvn form-control" id="splitit_cc_cid" name="cc_cid" value="" />
                    </div>
                    <a href="javascript:void(0)" class="cvv-what-is-this">{l s='What is this' mod='splitit'}
                <div class="cvc-info">
                {l s='The CVC (Card Validation Code) is a 3 or 4 digit code on the reverse side of Visa, MasterCard and Discover cards and on the front of American Express cards.' mod='splitit'}
                </div>
                    </a>
                </div></div>
            </li>
            <li class="num_installments">
              <div class="form-group">                            
                <label for="splitit_installments_no" class="required">{l s='Number of Installments' mod='splitit'}</label>
                <div class="input-box">
                    <select id="splitit_installments_no" name="installments_no" class="required-entry form-control">
                        <option value="">{l s='--Please Select--' mod='splitit'}</option>
                        {foreach from=$installments key=installment_key item=installment}
                            <option value="{$installment_key|escape:'htmlall':'UTF-8'}">{$installment|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div></div>
            </li>
            <li>
                <a class="installmentInit" onclick="getInstallmentPlans(baseUrl)">{l s='Click to see your personal payment schedule' mod='splitit'}</a>
                <span class="terms-condition-loader" style="display: none;"><img src="{$path|escape:'htmlall':'UTF-8'}views/img/opc-ajax-loader.gif"/></span>
            </li>
            <li class="terms-conditions">
                <input type="checkbox" id="splitit_terms" name="terms" class="required-entry" value="1" />
                <label for="splitit_terms" class="required">
                    {l s='I agree to the ' mod='splitit'}<a href="http://www.splitit.com/legal/customers-terms-and-conditions/" target="_blank">terms and conditions</a>
                </label>
            </li>
            <li>
                <div>
                    <img src="{$path|escape:'htmlall':'UTF-8'}views/img/splitit.png"/>
                </div>
            </li>
            <div class="clearfix"></div>
        </ul>

    </div>

	<p class="splitit_btn">
		<span id="splitit_confirm" class="btn btn-success pull-right btn-lg" onclick="confirmPayment(baseUrl)">{l s='I confirm my order' mod='splitit'}</span>
		<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="btn btn-success btn-lg">{l s='Other payment methods' mod='splitit'}</a>
	</p>

</form>

{literal}
<script>

//console.log(baseUrl);

$(document).ready(function(){

	login(baseUrl);
/*
	$('#splitit_confirm').on('click', function(e){
		
		// Validate form fields on submit
		validateFormFields();

	});*/

	jQuery(document).on("click","#payment_form_splitit li", function() {		

		if(isFormFieldsClicked == 0 && isClicked == false){
			login(baseUrl);
		}
    	
    });	


	$("#tnc").fancybox({
	    'width'         : '75%',
	    'height'        : '75%',
	    'autoScale'     : false,
	    'transitionIn'  : 'none',
	    'transitionOut' : 'none',
	    'type'          : 'iframe'
	});


});



</script>

{/literal}
    

{/if}






