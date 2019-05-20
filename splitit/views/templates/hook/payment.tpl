{*
*  @author Splitit
*  @copyright  2017-2018 Splitit
*  @since 1.6.0
*  @license BSD 2 License
*}

{if isset($outputHtml)}

	{literal}
	<script>
		 $(document).ready(function(){
		 	if($('.splitit_installment_price').length > 0){
		 		$('.splitit_installment_price').remove();
		 	}	
			$('#order-detail-content > #cart_summary > tfoot').append('<tr class="splitit_installment_price"><td colspan="7" class="text-right"><strong>{/literal}{$outputHtml|escape:"htmlall":"UTF-8"}{literal}</strong></td></tr>');
		 });
	</script>
	{/literal}

	{literal}
	<style>
	.splitit_installment_price > td.text-right {background: #d3d3d3;}
	.splitit_installment_price > td.text-right > strong {font-size:16px;}
	</style>
	{/literal}

{/if}

<p class="payment_module payment_module_splitit">
    <a href="{$link->getModuleLink('splitit', 'payment', [], true)|escape:'html'}" title="{l s='Splitit Payment' mod='splitit'}">
        <img src="{$path|escape:'htmlall':'UTF-8'}views/img/logo.png" alt="{l s='Monthly payments - 0% Interest' mod='splitit'}" />
        {l s='Monthly payments - 0% Interest' mod='splitit'}
    </a>
</p>