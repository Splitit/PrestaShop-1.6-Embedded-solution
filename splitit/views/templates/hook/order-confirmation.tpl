{*
*  @author Splitit
*  @copyright  2017-2018 Splitit
*  @since 1.6.0
*  @license BSD 2 License
*}

{if $splitit_order.valid == 1}
	<div class="conf confirmation">{l s='Congratulations, your payment has been approved and your order has been saved under the reference' mod='splitit'} <b>{$splitit_order.reference|escape:'htmlall':'UTF-8'}</b>.</div>
{/if}
