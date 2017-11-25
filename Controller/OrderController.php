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
 * Order class wrapper for PayPal module
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\OrderController
 */
class OrderController extends OrderController_parent
{
    /** @var null \OxidEsales\OpenPay\Module\Core\Config */
    protected $openPayConfig = null;

    /**
     * Returns next order step. If ordering was sucessfull - returns string "thankyou" (possible
     * additional parameters), otherwise - returns string "payment" with additional
     * error parameters.
     *
     * @param integer $iSuccess status code
     *
     * @return  string  $sNextStep  partial parameter url for next step
     */
    protected function _getNextStep($iSuccess)
    {
        $getNextStep = parent::_getNextStep($iSuccess);

        $oOpenpay = $this->initOpenPay();
        $sCustomer =  $this->getSession()->getVariable('customerid');

        $oOrder = $this->getOrder();

        $aChargeData = array(
            'source_id' => $this->getSession()->getVariable('tokenid'),
            'method' => 'card',
            'amount' => $oOrder->oxorder__oxtotalordersum->rawValue,
            'description' => $oOrder->oxorder__oxpaymentid->rawValue,
            'order_id' => $oOrder->oxorder__oxordernr->rawValue,
            'device_session_id' => $this->getSession()->getVariable('deviceid'),
        );

        $customer = $oOpenpay->customers->get($sCustomer);
        $charge = $customer->charges->create($aChargeData);

        //$this->markOrderPaid($charge);

        return $getNextStep;

    }

    /**
     * Returns current order object
     *
     * @return \OxidEsales\Eshop\Application\Model\Order
     */
    protected function markOrderPaid($charge)
    {
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);

        $oOrder->markOrderPaid($charge);

    }


    /**
     * Returns current order object
     *
     * @return \OxidEsales\Eshop\Application\Model\Order
     */
    protected function getOrder()
    {
        $order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $order->load($this->getSession()->getVariable('sess_challenge'));

        return $order;
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
     * Returns OpenPay Object.
     *
     * @return object
     */
    public function initOpenPay()
    {
        $sOpenPayId = $this->getOpenPayConfig()->getOpenPayApiId();
        $sPrivateApiKey = $this->getOpenPayConfig()->getOpenPayPrivateKey();

        $openpay = Openpay::getInstance($sOpenPayId, $sPrivateApiKey);
        return $openpay;

    }

}
