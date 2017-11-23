<?php
/**
 * This file is part of Weetsi OpenPay module.
 *
 * Weetsi OpenPay module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Weetsi OpenPay module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Weetsi OpenPay module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.weetsi.com
 * @copyright (C) Weetsi 2017
 * @version   Weetsi OpenPay
 */

namespace OxidEsales\OpenPayModule\Controller;

use Openpay;

/**
 * Payment class wrapper for PayPal module
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\PaymentController
 */
class PaymentController extends PaymentController_parent
{
    /** @var null \OxidEsales\OpenPay\Module\Core\Config */
    protected $openPayConfig = null;


    /**
     */
    public function validatePayment()
    {
        $oOpenpay = $this->initOpenPay();
        $oUser = $this->getUser();

        $aDynvalue =  $this->getConfig()->getRequestParameter( 'dynvalue');

        $sUserCountryId = $oUser->oxuser__oxcountryid->getRawValue();
        $sUserState = $oUser->oxuser__oxstateid->getRawValue();


        $customerData = [
              //'external_id' => $oUser->oxuser__oxid->getRawValue(),
                'name' => $oUser->oxuser__oxfname->getRawValue(),
                'last_name' => $oUser->oxuser__oxlname->getRawValue(),
                'email' => $oUser->oxuser__oxusername->getRawValue(),
                'phone_number' => $oUser->oxuser__oxfon->getRawValue(),
                'address' => [
                    'line1' =>  $oUser->oxuser__oxstreet->getRawValue(),
                    'line2' =>  $oUser->oxuser__oxstreetnr->getRawValue(),
                    'line3' =>  $oUser->oxuser__oxaddinfo->getRawValue(),
                    'postal_code' =>  $oUser->oxuser__oxzip->getRawValue(),
                    'state' =>  $sUserState ?: 'DF',
                    'city' =>  $oUser->oxuser__oxcity->getRawValue(),
                    'country_code' =>  $this->getUserCountryCode($sUserCountryId),
                ]
            ];

        $customer = $oOpenpay->customers->add($customerData);
        $card = $customer->cards->add($aDynvalue);

        return parent::validatePayment($card);

    }

    /**
     * Returns current URL.
     *
     * @return string
     */
    public function initOpenPay()
    {
        $sOpenPayId = $this->getOpenPayConfig()->getOpenPayApiId();
        $sPrivateApiKey = $this->getOpenPayConfig()->getOpenPayPrivateKey();

        $openpay = Openpay::getInstance($sOpenPayId, $sPrivateApiKey);
        return $openpay;

    }

    /**
     * Returns OpenPay config.
     *
     * @return \OxidEsales\OpenPayModule\Core\Config
     */
    protected function getOpenPayConfig()
    {
        if (is_null($this->openPayConfig)) {
            $this->openPayConfig = oxNew(\OxidEsales\OpenPayModule\Core\Config::class);
        }
        return $this->openPayConfig;
    }

    /**
     * Returns current OpenPay Id.
     *
     * @return string
     */
    public function getOpenPayId()
    {
        return $this->getOpenPayConfig()->getOpenPayApiId();
    }


    public function getUserCountryCode( $sCountryId = '' )
    {
        /** @var oxCountry $oCountry */
        $oCountry = oxNew( 'oxCountry' );

        $oCountry->load( empty( $sCountryId ) ? $this->oxuser__oxcountryid->value : (string) $sCountryId );

        return $oCountry->oxcountry__oxisoalpha2->value;
    }

    /**
     * Assign  note payment values to view data. Loads user note payment
     * if available and assigns payment data to $this->_aDynValue
     */
    protected function _assignDebitNoteParams()
    {
        // #701A
        $oUserPayment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
        //such info available ?
        if ($oUserPayment->getPaymentByPaymentType($this->getUser(), 'oxiddebitnote')) {
            $sUserPaymentField = 'oxuserpayments__oxvalue';
            $aAddPaymentData = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oUserPayment->$sUserPaymentField->value);

            //checking if some of values is allready set in session - leave it
            foreach ($aAddPaymentData as $oData) {
                if (!isset($this->_aDynValue[$oData->name]) ||
                    (isset($this->_aDynValue[$oData->name]) && !$this->_aDynValue[$oData->name])
                ) {
                    $this->_aDynValue[$oData->name] = $oData->value;
                }
            }
        }
    }
}
