[{if $oView->isLowOrderPrice()}]
    <div class="alert alert-info">
        <b>[{oxmultilang ident="MIN_ORDER_PRICE"}] [{$oView->getMinOrderPrice()}] [{$currency->sign}]</b>
    </div>
    [{else}]

    <div class="well well-sm cart-buttons">
        <a href="[{oxgetseourl ident=$oViewConf->getOrderLink()}]" class="btn btn-default pull-left prevStep submitButton largeButton" id="paymentBackStepBottom"><i class="fa fa-caret-left"></i> [{oxmultilang ident="PREVIOUS_STEP"}]</a>
        <button type="submit"  class="btn btn-primary pull-right submitButton nextStep largeButton" id="paymentNextStepBottom" >[{oxmultilang ident="CONTINUE_TO_NEXT_STEP"}] <i class="fa fa-caret-right"></i></button>
        <div class="clearfix"></div>
    </div>
    [{/if}]

[{oxscript add='$(".paypalHelpIcon").click(function (){return false;});'}]


