[{*$oViewConf->getOpenPayId()|var_dump*}]
[{assign var="sOpenPayId" value=$oViewConf->getOpenPayId()}]
[{assign var="sPublicKey" value=$oViewConf->getOpenPayPublicKey()}]
[{assign var="blOpenPaySandbox" value=$oViewConf->isSandboxEnabled()}]


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay.v1.min.js"></script>

<script type="text/javascript">
    [{capture append="aCustomScripts"}]
    $( function()
        {
            $(document).ready(function() {

                OpenPay.setId('[{$sOpenPayId}]');
                OpenPay.setApiKey('[{$sPublicKey}]');
                OpenPay.setSandboxMode('[{$blOpenPaySandbox}]');

                $('#paymentNextStepBottom').on('click', function(event) {
                    event.preventDefault();
                    //$("#paymentNextStepBottom").prop( "disabled", true);
                    OpenPay.token.extractFormAndCreate('payment', success_callbak, error_callbak);
                });

                var success_callbak = function(response) {
                    var token_id = response.data.id;
                    $('#token_id').val(token_id);
                    $('#payment').submit();
                };

                var error_callbak = function(response) {
                    var desc = response.data.description != undefined ? response.data.description : response.message;
                    alert("ERROR [" + response.status + "] " + desc);
                    $("#save-button").prop("disabled", false);
                };
            })
        }
    );
    [{/capture}]
</script>

[{foreach from=$aCustomScripts item="sScript"}]
[{oxscript add=$sScript priority=10}]
[{/foreach}]


[{if $sPaymentID == "openpaycredit"}]
    [{assign var="dynvalue" value=$oView->getDynValue()}]

[{elseif $sPaymentID != "openpaycredit"}]
    [{$smarty.block.parent}]
[{/if}]
[{assign var="dynvalue" value=$oView->getDynValue()}]
 <dl>
    <dt>
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
        <label for="payment_[{$sPaymentID}]"><b>[{$paymentmethod->oxpayments__oxdesc->value}]</b></label>
    </dt>
    <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">

        <input type="hidden" name="token_id" id="token_id"/>

        <div class="form-group">
            <label class="req control-label col-lg-3">[{oxmultilang ident="NUMBER"}]</label>
            <div class="col-lg-9">
                <input type="text" class="form-control js-oxValidate js-oxValidate_notEmpty" size="20" maxlength="64" name="dynvalue[kknumber]" data-openpay-card="card_number" value="[{$dynvalue.kknumber}]" required="required">
            </div>
        </div>

        <div class="form-group">
            <label class="req control-label col-lg-3">[{oxmultilang ident="BANK_ACCOUNT_HOLDER"}]</label>
            <div class="col-lg-9">
                <input type="text" size="20" class="form-control js-oxValidate js-oxValidate_notEmpty" maxlength="64" name="dynvalue[kkname]" data-openpay-card="holder_name" value="[{if $dynvalue.kkname}][{$dynvalue.kkname}][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]" required="required">
                <span class="help-block">[{oxmultilang ident="IF_DIFFERENT_FROM_BILLING_ADDRESS"}]</span>
            </div>
        </div>

        <div class="form-group">
            <label class="req control-label col-xs-12 col-lg-3">[{oxmultilang ident="VALID_UNTIL"}]</label>
            <div class="col-xs-6 col-lg-2">
                <input type="text" size="2" name="dynvalue[kkmonth]" data-openpay-card="expiration_month"  value="[{$dynvalue.kkmonth}]" required="required"/> /
            </div>

            <div class="col-xs-6 col-lg-2">
                <input type="text" size="2" name="dynvalue[kkyear]" data-openpay-card="expiration_year"  value="[{$dynvalue.kkyear}]" required="required"/>
            </div>
        </div>

        <div class="form-group">
            <label class="req control-label col-lg-3">[{oxmultilang ident="CARD_SECURITY_CODE"}]</label>
            <div class="col-lg-6">
                <input type="text" class="form-control" size="4" maxlength="3" name="dynvalue[kkpruef]" data-openpay-card="cvv2" required="required">
                <span class="help-block">[{oxmultilang ident="CARD_SECURITY_CODE_DESCRIPTION"}]</span>
            </div>
        </div>

        <div class="clearfix"></div>

        [{block name="checkout_payment_longdesc"}]
        [{if $paymentmethod->oxpayments__oxlongdesc->value}]
        <div class="alert alert-info col-lg-offset-3 desc">
            [{$paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
        </div>
        [{/if}]
        [{/block}]
    </dd>
</dl>*

