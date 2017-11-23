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
     * Checks if payment action is processed by PayPal
     *
     * @return bool
     */
    public function execute()
    {
        $oOpenpay = $this->initOpenPay();
        $oUser = $this->getUser();

        $chargeData = array(
            'source_id' => 'tvyfwyfooqsmfnaprsuk',
            'method' => 'card',
            'amount' => 100,
            'description' => 'Cargo inicial a mi cuenta',
            'order_id' => 'ORDEN-00070');

        $customer = $oOpenpay->customers->get('a9ualumwnrcxkl42l6mh');
        $charge = $customer->charges->create($chargeData);


        die();
        return parent::execute();
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
}
