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

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'          => 'weeopenpay',
    'title'       => 'OpenPay',
    'description' => [
        'de' => '',
        'en' => '',
    ],
    'thumbnail'   => 'logo.png',
    'version'     => '0.0.1',
    'author'      => 'Alejandro Sanchez',
    'url'         => 'http://www.weetsi.com',
    'email'       => 'info@weetsi.com',
    'extend'      => [
        \OxidEsales\Eshop\Core\ViewConfig::class => \OxidEsales\OpenPayModule\Core\ViewConfig::class,

    ],
    'controllers'       => [


    ],
    'templates'   => [


    ],
    'events'      => [

    ],
    'blocks'      => [
        ['template' => 'page/checkout/payment.tpl',
            'block'=>'select_payment',
            'file'=>'/views/blocks/page/checkout/weeopenpaypaymentselector.tpl'
        ],

    ],
    'settings'    => [
        [
            'group' => 'weeopenpay_production',
            'name'  => 'sWeeOpenPayProdId',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'weeopenpay_production',
            'name'  => 'sWeeOpenPayProdPublicKey',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'weeopenpay_production',
            'name'  => 'sWeeOpenPayProdPrivateKey',
            'type'  => 'password',
            'value' => ''
        ],
        [
            'group' => 'weeopenpay_production',
            'name'  => 'sWeeOpenPayProdUrl',
            'type'  => 'str',
            'value' => ''
        ],

        [
            'group' => 'weeopenpay_sandbox',
            'name'  => 'blOpenPayLoggerEnabled',
            'type'  => 'bool',
            'value' => 'false'
        ],
        [
            'group' => 'weeopenpay_sandbox',
            'name'  => 'blWeeOpenPaySandboxMode',
            'type'  => 'bool',
            'value' => 'false'
        ],
        [
            'group' => 'weeopenpay_sandbox',
            'name'  => 'sWeeOpenPaySandboxId',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'weeopenpay_sandbox',
            'name'  => 'sWeeOpenPaySandboxPublicKey',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'weeopenpay_sandbox',
            'name'  => 'sWeeOpenPaySandboxPrivateKey',
            'type'  => 'password',
            'value' => ''
        ],
        [
            'group' => 'weeopenpay_sandbox',
            'name' => 'sWeeOpenPaySandboxUrl',
            'type' => 'str',
            'value' => ''
        ],
    ]
];