[{*$oViewConf->getOpenPayId()|var_dump*}]
[{assign var="sOpenPayId" value=$oViewConf->getOpenPayId()}]
[{assign var="sPublicKey" value=$oViewConf->getOpenPayPublicKey()}]
[{assign var="blOpenPaySandbox" value=$oViewConf->isSandboxEnabled()}]

[{if $sPaymentID == "openpaycredit"}]
    [{assign var="dynvalue" value=$oView->getDynValue()}]
[{elseif $sPaymentID != "openpaycredit"}]
    [{$smarty.block.parent}]
[{/if}]
 <dl>
    <dt>
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
        <label for="payment_[{$sPaymentID}]"><b>[{$paymentmethod->oxpayments__oxdesc->value}]</b></label>
    </dt>
    <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">

        <input type="hidden" name="token_id" id="token_id"/>

        <div class="form-group">
            <label class="req control-label col-lg-3">[{oxmultilang ident="NUMBER"}]</label>
            <div class="col-lg-6">
                <input x-autocompletetype="cc-number" type="text" class="form-control js-oxValidate js-oxValidate_notEmpty" size="20" maxlength="64" name="dynvalue[card_number]" value="4111111111111111" data-openpay-card="card_number" required="required">
            </div>
        </div>

        <div class="form-group">
            <label class="req control-label col-lg-3">[{oxmultilang ident="BANK_ACCOUNT_HOLDER"}]</label>
            <div class="col-lg-6">
                <input type="text" size="20" class="form-control js-oxValidate js-oxValidate_notEmpty" maxlength="64" name="dynvalue[holder_name]" value="[{if $dynvalue.kkname}][{$dynvalue.kkname}][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]" data-openpay-card="holder_name" required="required">
                <span class="help-block">[{oxmultilang ident="IF_DIFFERENT_FROM_BILLING_ADDRESS"}]</span>
            </div>
        </div>

        <div class="form-group">
            <label class="req control-label col-xs-12 col-lg-3">[{oxmultilang ident="VALID_UNTIL"}]</label>
            <div class="col-xs-3 col-lg-2">
                <select name="dynvalue[expiration_month]" data-openpay-card="expiration_month" class="form-control selectpicker" required="required">
                    <option [{if $dynvalue.kkmonth == "01"}]selected[{/if}]>01</option>
                    <option [{if $dynvalue.kkmonth == "02"}]selected[{/if}]>02</option>
                    <option [{if $dynvalue.kkmonth == "03"}]selected[{/if}]>03</option>
                    <option [{if $dynvalue.kkmonth == "04"}]selected[{/if}]>04</option>
                    <option [{if $dynvalue.kkmonth == "05"}]selected[{/if}]>05</option>
                    <option [{if $dynvalue.kkmonth == "06"}]selected[{/if}]>06</option>
                    <option [{if $dynvalue.kkmonth == "07"}]selected[{/if}]>07</option>
                    <option [{if $dynvalue.kkmonth == "08"}]selected[{/if}]>08</option>
                    <option [{if $dynvalue.kkmonth == "09"}]selected[{/if}]>09</option>
                    <option [{if $dynvalue.kkmonth == "10"}]selected[{/if}]>10</option>
                    <option [{if $dynvalue.kkmonth == "11"}]selected[{/if}]>11</option>
                    <option [{if $dynvalue.kkmonth == "12"}]selected[{/if}]>12</option>
                </select>
            </div>
            <div class="col-xs-3 col-lg-2">
                <select name="dynvalue[expiration_year]" class="form-control selectpicker" data-openpay-card="expiration_year" required="required">
                    [{foreach from=$oViewConf->getCreditYears() item=year}]
                    <option [{if $dynvalue.kkyear == $year}]selected[{/if}]>[{$year}]</option>
                    [{/foreach}]
                </select>
            </div>
            <div class="col-sm-3"></div>
        </div>

        <div class="form-group">
            <label class="req control-label col-lg-3">[{oxmultilang ident="CARD_SECURITY_CODE"}]</label>
            <div class="col-lg-4">
                <input type="text" class="form-control js-oxValidate js-oxValidate_notEmpty" size="20" maxlength="64" name="dynvalue[cvv2]" value="[{$dynvalue.cvv2}]" data-openpay-card="cvv2" required="required">
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
</dl>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="https://openpay.s3.amazonaws.com/openpay.v1.min.js"></script>
<script type='text/javascript' src="https://openpay.s3.amazonaws.com/openpay-data.v1.min.js"></script>

<script type="text/javascript">
    [{capture append="aCustomScripts"}]
    $( function()
        {
            $(document).ready(function() {

                OpenPay.setId('[{$sOpenPayId}]');
                OpenPay.setApiKey('[{$sPublicKey}]');
                OpenPay.setSandboxMode('[{$blOpenPaySandbox}]');

                var deviceSessionId = OpenPay.deviceData.setup('payment', 'device_session_id');

                console.dir(deviceSessionId);

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

