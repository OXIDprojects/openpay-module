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
     * Detects is current payment must be processed by PayPal and instead of standard validation
     * redirects to standard PayPal dispatcher
     *
     * @return bool
     */
    public function validatePayment()
    {
        $oOpenpay = $this->initOpenPay();
        $aUserData = $this->getUser();

        $aDynData = $this->getConfig()->getRequestParameter("dynvalue");

        $cardData = array(
            'holder_name'       =>    $aDynData["kkname"],
            'card_number'       =>    $aDynData["kknumber"],
            'cvv2'              =>    $aDynData["kkpruef"],
            'expiration_month'  =>    $aDynData["kkmonth"],
            'expiration_year'   =>    $aDynData["kkyear"],
            'address' => array(
                'line1' =>  $aUserData['oxstreet'],
                'line2' =>  $aUserData['oxstreetnr'],
                'line3' =>  $aUserData['oxaddinfo'],
                'postal_code' =>  $aUserData['oxzip'],
                'state' =>  $aUserData['oxstateid'],
                'city' =>  $aUserData['oxcity'],
                'country_code' =>  $aUserData['oxcountryid']));


        $oOpenpay->cards->add($cardData);

        return parent::validatePayment();

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



    /**
     * Returns current URL.
     *
     * @return string
     */
    public function isConfirmedByPayPal($basket)
    {


    }
}
