{*
*  @author Splitit
*  @copyright  2017-2018 Splitit
*  @since 1.6.0
*  @license BSD 2 License
*}

<div class="splitit_installment_price"><strong>{$outputHtml|escape:'htmlall':'UTF-8'}</strong></div>
{literal} 
	<script>
		$('ul').each(function(i, items_list){
		    $(items_list).find('li').each(function(j, li){ 
		        var current = $(this);
		        $(current).find("div.splitit_installment_price").not(':first').hide();
		    });
		});
	</script>
{/literal}