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

namespace OxidEsales\OpenPayModule\Core;

use Openpay;

/**
 * ViewConfig class wrapper for OpenPay module.
 *
 * @mixin \OxidEsales\Eshop\Core\ViewConfig
 */
class ViewConfig extends ViewConfig_parent
{
    /** @var null \OxidEsales\OpenPay\Module\Core\Config */
    protected $openPayConfig = null;


    /**
     * OpenPay payment object.
     *
     * @var \OxidEsales\Eshop\Application\Model\Payment|bool
     */
    protected $openPayPayment = null;

    /**
     * Status if OpenPay is ON.
     *
     * @var bool
     */
    protected $openPayEnabled = null;

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
     * Returns current OpenPay Public API Key.
     *
     * @return string
     */
    public function getOpenPayPublicKey()
    {
        return $this->getOpenPayConfig()->getOpenPayPublicKey();
    }

    /**
     * Returns current OpenPay Public API Key.
     *
     * @return string
     */
    public function isSandboxEnabled()
    {
        return $this->getOpenPayConfig()->isSandboxEnabled();
    }

    /**
     * Returns current URL.
     *
     * @return string
     */
    public function getOpenPayApiUrl()
    {
        return $this->getOpenPayConfig()->getOpenPaySandboxApiUrl();
    }
}
