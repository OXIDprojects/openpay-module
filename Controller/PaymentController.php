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
    protected $openPay = null;

    private $_sSaveError = null;


    /**
     * OpenPay constructor
     */
    public function init()
    {
        parent::init();

        $sOpenPayId = $this->getOpenPayConfig()->getOpenPayApiId();
        $sPrivateApiKey = $this->getOpenPayConfig()->getOpenPayPrivateKey();

        $this->openPay = Openpay::getInstance($sOpenPayId, $sPrivateApiKey);
    }

    /**
     */
    public function validatePayment()
    {

            $session = $this->getSession();

            $sTokenId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('token_id');
            $sDeviceId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('device_session_id');

            $this->getOpenPayCustomer();
            $oCustomer = $this->getOpenPayCustomer();

            if (!$oCustomer) {
                $oCustomer = $this->addOpenPayCustomer();
            }

            $card = $this->getOpenPayCards($oCustomer);

            if (!$card) {
                $card = $this->addOpenPayCard($oCustomer);
            }

            $paymentId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('paymentid');

            if ($paymentId === 'openpaycredit') {
                $session->setVariable('paymentid', 'openpaycredit');
                $session->setVariable('tokenid', $sTokenId);
                $session->setVariable('deviceid', $sDeviceId);
                $session->setVariable('customerid', $card->customer_id);
            }

            return parent::validatePayment();
    }


    /**
     * Adds Card.
     *
     * @return object
     */
    public function addOpenPayCard($oCustomer)
    {
        $aCardData=   \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('dynvalue');

        $customer = $oCustomer;
        $card = $customer->cards->add($aCardData);

        return $card;

    }

    /**
     * Get Cards.
     *
     * @return object
     */
    public function getOpenPayCards($oCustomer)
    {
        $aCardData=   \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter( 'dynvalue');


        $cardNr = $aCardData["card_number"];
        $sCardNumber = substr_replace($cardNr,  str_repeat("X", 6), 6, 6);

        $findData = array(
            'offset' => 0,
            'limit' => 5);

        $cardList = $oCustomer->cards->getList($findData);

        if($cardList){
            foreach ($cardList as $card) {
                if($sCardNumber == $card->card_number){
                    return $card;
                }
            }
        }

        return false;

    }

    /**
     * Returns Customer  Id.
     *
     * @return object
     */
    public function getOpenPayCustomer()
    {
        $oOpenpay = $this->openPay;
        $oUser = $this->getUser();

        $findCustomer = array(
            'external_id' => $oUser->oxuser__oxid->getRawValue(),
        );

        $customer = $oOpenpay->customers->getList($findCustomer);

        if($customer){
            return $customer[0];
        }

        return false;
    }
    /**
     * Adds Customer.
     *
     * @return object
     */
    public function addOpenPayCustomer()
    {
        $oOpenpay = $this->openPay;
        $oUser = $this->getUser();

        $sUserCountryId = $oUser->oxuser__oxcountryid->getRawValue();
        $sUserState = $oUser->oxuser__oxstateid->getRawValue();

        $aCustomerData = [
            'external_id' => $oUser->oxuser__oxid->getRawValue(),
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

        return $oOpenpay->customers->add($aCustomerData);
    }

    /**
     * Adds Customer.
     *
     * @return object
     */
    public function deleteOpenPayCustomer($count = 1)
    {
        $oOpenpay = $this->openPay;

        $findDataRequest = array(
            'creation[gte]' => '2017-01-31',
            'limit' => $count,
        );

        $customerList = $oOpenpay->customers->getList($findDataRequest);

        foreach ($customerList as $customer){
            $customer = $oOpenpay->customers->get($customer->id);
            $customer->delete();
        }
        return;
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

}
