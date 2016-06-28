<p class="payment_module">
	<a href="javascript:$('#securepay_form_wywallet').submit();" title="{l s='Betala via Payer' mod='payer_wywallet'}">
		<img src="{$module_dir}logo_small_wywallet.png" style="float:left" alt="{l s='Betala via Payer' mod='payer_wywallet'}" />
		<strong>{l s='Betala via Payer' mod='payer_wywallet'}</strong><br />{l s='Betala via wywallet' mod='payer_wywallet'}<br style="clear:both;" />
	</a>
</p>


<form action="{$securepayUrl}" method="post" id="securepay_form_wywallet" class="hidden">
	<input type="hidden" name="payread_agentid" value="{$payread_agentid}" />
	<input type="hidden" name="payread_xml_writer" value="{$payread_xml_writer}" />
	<input type="hidden" name="payread_data" value="{$payread_data}" />
	<input type="hidden" name="payread_checksum" value="{$payread_checksum}" />		
</form>
{literal}
<script text="text/javascript">
var elm = '';
var oldelm = '';
function check(){
elm = document.getElementById("total_price").textContent;
if(elm != oldelm && oldelm != ''){
    window.location.reload();
}
oldelm = elm;
setTimeout("check()",100);
}
check();
</script>
{/literal}