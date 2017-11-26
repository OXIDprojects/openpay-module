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

namespace OxidEsales\OpenPayModule\Model;

use Openpay;

/**
 * OpenPay PaymentGateway class
 *  Checks and sets payment method data, executes payment.
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Order
 */
class PaymentGateway extends PaymentGateway_parent
{
    /**
     * Overrides standard oxid finalizeOrder method if the used payment method belongs to OpenPay.
     * Return parent's return if payment method is no OpenPay method
     *
     * Executes payment, returns true on success.
     *
     * @param double $dAmount Goods amount
     * @param object &$oOrder User ordering object
     *
     * @extend executePayment
     * @return bool
     */
    public function executePayments( $dAmount, &$oOrder )
    {
        $success = parent::executePayment($dAmount, $oOrder);
        $paymentId = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('paymentid');

        if ($paymentId == 'openpaycredit') {
          //return $this->doOpenPayCharge($dAmount, $oOrder);
        }

        return $success;
    }

    /**
     * @return array
     */
    public function doOpenPayCharges($dAmount, $oOrder)
    {
        $oOpenpay = $this->initOpenPay();
        $sCustomer =  $this->getSession()->getVariable('customerid');
        $orderId = $oOrder->oxorder__oxid->value;

        if (!$oOrder->oxorder__oxordernr->value) {
            $oOrderModel = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $oOrderModel->load($orderId);

            $oOrderModel->_setNumber();
        }

        $aChargeData = array(
            'source_id' => $this->getSession()->getVariable('tokenid'),
            'method' => 'card',
            'amount' => $dAmount,
            'description' => $oOrder->oxorder__oxid->value,
            'capture' => false,
            'order_id' => $this->oxorder__oxordernr->value,
            'device_session_id' => $this->getSession()->getVariable('deviceid'),
        );


        $customer = $oOpenpay->customers->get($sCustomer);
        $charge = $customer->charges->create($aChargeData);

        $session = $this->getSession();
        $session->setVariable('chargeid', $charge->id);

        return true;
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
