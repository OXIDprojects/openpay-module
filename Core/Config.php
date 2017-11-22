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
 * OpenPay config class
 */
class Config
{
    /**
     * OpenPay api id.
     *
     * @var string
     */
    protected $openPayId = null;



    /**
     * OpenPay module id.
     *
     * @var string
     */
    protected $openPayPrivateKey = null;

    /**
     * OpenPay module id.
     *
     * @var string
     */
    protected $openPayPublicKey = null;

    /**
     * OpenPay sandbox API url.
     *
     * @var string
     */
    protected $openPaySandboxApiUrl = 'https://sandbox-api.openpay.mx/v1/';

    /**
     * OpenPay API url.
     *
     * @var string
     */
    protected $openPayApiUrl = 'https://api.openpay.mx/v1/';


    /**
 *  OpenPay Id setter
 *
 * @param string $OpenPayId
 */
    public function setOpenPayApiId($OpenPayId)
    {
        $this->openPayId = $OpenPayId;
    }

    /**
     * OpenPay Id getter
     *
     * @return string
     */
    public function getOpenPayApiId()
    {
        if ($this->isSandboxEnabled()) {
            $ApiId = $this->getConfig()->getConfigParam('sWeeOpenPaySandboxId');
        }else{
            $ApiId = $this->getConfig()->getConfigParam('sWeeOpenPayProdId');
        }

        if ($ApiId) {
            $this->setOpenPayApiId($ApiId);
        }

        return $this->openPayId;
    }


    /**
     * Public API Key setter
     *
     * @return string
     */
    public function setOpenPayPublicKey($openPayPublicKey)
    {
        $this->openPayPublicKey = $openPayPublicKey;
    }

    /**
     *  Public API Key setter
     *
     * @return string
     */
    public function getOpenPayPublicKey()
    {
        if ($this->isSandboxEnabled()) {
            $key = $this->getConfig()->getConfigParam('sWeeOpenPaySandboxPublicKey');
        }else{
            $key = $this->getConfig()->getConfigParam('sWeeOpenPayProdPublicKey');
        }

        if ($key) {
            $this->setOpenPayPublicKey($key);
        }

        return $this->openPayPublicKey;
    }

    /**
     * Public API Key setter
     *
     * @return string
     */
    public function setOpenPayPrivateKey($openPayPrivateKey)
    {
        $this->openPayPrivateKey = $openPayPrivateKey;
    }

    /**
     *  Public API Key setter
     *
     * @return string
     */
    public function getOpenPayPrivateKey()
    {
        if ($this->isSandboxEnabled()) {
            $key = $this->getConfig()->getConfigParam('sWeeOpenPaySandboxPrivateKey');
        }else{
            $key = $this->getConfig()->getConfigParam('sWeeOpenPayProdPrivateKey');
        }

        if ($key) {
            $this->setOpenPayPrivateKey($key);
        }

        return $this->openPayPrivateKey;
    }




    /**
     * OpenPay sandbox api url setter
     *
     * @param string $openPaySandboxApiUrl
     */
    public function setOpenPaySandboxApiUrl($openPaySandboxApiUrl)
    {
        $this->openPaySandboxApiUrl = $openPaySandboxApiUrl;
    }

    /**
     * OpenPay sandbox api url getter
     *
     * @return string
     */
    public function getOpenPaySandboxApiUrl()
    {
        $url = $this->getConfig()->getConfigParam('sWeeOpenPaySandboxUrl');
        if ($url) {
            $this->setOpenPaySandboxApiUrl($url);
        }

        return $this->openPaySandboxApiUrl;
    }

    /**
     * Returns true of sandbox mode is ON
     *
     * @return bool
     */
    public function isSandboxEnabled()
    {
        return $this->getConfig()->getConfigParam('blWeeOpenPaySandboxMode');

    }


    /**
     * Returns active shop id
     *
     * @return string
     */
    protected function getShopId()
    {
        return $this->getConfig()->getShopId();
    }

    /**
     * Returns oxConfig instance
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    protected function getConfig()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig();
    }

}
