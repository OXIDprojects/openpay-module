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

        $cardData =  $this->getConfig()->getRequestParameter( 'dynvalue');

        $sUserCountryId = $oUser->oxuser__oxcountryid->getRawValue();
        $sUserCountry = $oUser->getUserCountry($sUserCountryId)->value;

        $customerData = [
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
                'state' =>  $oUser->oxuser__oxstateid->getRawValue(),
                'city' =>  $oUser->oxuser__oxcity->getRawValue(),
                'country_code' =>  $this->getUserCountryCode($sUserCountryId),
            ]
        ];

        $customer = $oOpenpay->customers->add($customerData);
        $card = $customer->cards->add($cardData);

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


    /**
     * Returns oCustomer
     *
     * @return string
     */
    public function setCustomer()
    {
        $customerData = array(
            'name' => 'Teo',
            'last_name' => 'Velazco',
            'email' => 'teofilo@payments.com',
            'phone_number' => '4421112233',
            'address' => array(
                'line1' => 'Privada Rio No. 12',
                'line2' => 'Co. El Tintero',
                'line3' => '',
                'postal_code' => '76920',
                'state' => 'Querétaro',
                'city' => 'Querétaro.',
                'country_code' => 'MX'));

        $customer = $openpay->customers->add($customerData);

        return $customer;
    }

    public function getUserCountryCode( $sCountryId = '' )
    {
        /** @var oxCountry $oCountry */
        $oCountry = oxNew( 'oxCountry' );

        $oCountry->load( empty( $sCountryId ) ? $this->oxuser__oxcountryid->value : (string) $sCountryId );

        return $oCountry->oxcountry__oxisoalpha2->value;
    }
}
