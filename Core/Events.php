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

/**
 * Class defines what module does on Shop events.
 */
class Events
{
    /**
     * Add additional fields: payment status, captured amount, refunded amount in oxOrder table
     */
    public static function addOrderTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `weeopenpay_order` (
              `WEEOPENPAY_ORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `WEEOPENPAY_PAYMENTSTATUS` enum('pending','completed','failed','canceled') NOT NULL DEFAULT 'pending',
              `WEEOPENPAY_CAPTUREDAMOUNT` decimal(9,2) NOT NULL,
              `WEEOPENPAY_REFUNDEDAMOUNT` decimal(9,2) NOT NULL,
              `WEEOPENPAY_VOIDEDAMOUNT`   decimal(9,2) NOT NULL,
              `WEEOPENPAY_TOTALORDERSUM`  decimal(9,2) NOT NULL,
              `WEEOPENPAY_CURRENCY` varchar(32) NOT NULL,
              `WEEOPENPAY_TRANSACTIONMODE` enum('Sale','Authorization') NOT NULL DEFAULT 'Sale',
              `WEEOPENPAY_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              PRIMARY KEY (`WEEOPENPAY_ORDERID`),
              KEY `WEEOPENPAY_PAYMENTSTATUS` (`WEEOPENPAY_PAYMENTSTATUS`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Add OpenPay payment method set EN and DE long descriptions
     */
    public static function addPaymentMethod()
    {
        $paymentDescriptionsCredit = array(
            'en' => '<div>When selecting this payment method you are being redirected to OpenPay where you can login into your account or open a new account. In OpenPay you are able to authorize the payment. As soon you have authorized the payment, you are again redirected to our shop where you can confirm your order.</div> <div style="margin-top: 5px">Only after confirming the order, transfer of money takes place.</div>',
            'de' => '<div>Bei Auswahl der Zahlungsart OpenPay werden Sie im nächsten Schritt zu OpenPay weitergeleitet. Dort können Sie sich in Ihr OpenPay-Konto einloggen oder ein neues OpenPay-Konto eröffnen und die Zahlung autorisieren. Sobald Sie Ihre Daten für die Zahlung bestätigt haben, werden Sie automatisch wieder zurück in den Shop geleitet, um die Bestellung abzuschließen.</div> <div style="margin-top: 5px">Erst dann wird die Zahlung ausgeführt.</div>'
        );

        $paymentDescriptionsDebit = array(
            'en' => '<div>When selecting this payment method you are being redirected to OpenPay where you can login into your account or open a new account. In OpenPay you are able to authorize the payment. As soon you have authorized the payment, you are again redirected to our shop where you can confirm your order.</div> <div style="margin-top: 5px">Only after confirming the order, transfer of money takes place.</div>',
            'de' => '<div>Bei Auswahl der Zahlungsart OpenPay werden Sie im nächsten Schritt zu OpenPay weitergeleitet. Dort können Sie sich in Ihr OpenPay-Konto einloggen oder ein neues OpenPay-Konto eröffnen und die Zahlung autorisieren. Sobald Sie Ihre Daten für die Zahlung bestätigt haben, werden Sie automatisch wieder zurück in den Shop geleitet, um die Bestellung abzuschließen.</div> <div style="margin-top: 5px">Erst dann wird die Zahlung ausgeführt.</div>'
        );

        $paymentDescriptionsStores = array(
            'en' => '<div>When selecting this payment method you are being redirected to OpenPay where you can login into your account or open a new account. In OpenPay you are able to authorize the payment. As soon you have authorized the payment, you are again redirected to our shop where you can confirm your order.</div> <div style="margin-top: 5px">Only after confirming the order, transfer of money takes place.</div>',
            'de' => '<div>Bei Auswahl der Zahlungsart OpenPay werden Sie im nächsten Schritt zu OpenPay weitergeleitet. Dort können Sie sich in Ihr OpenPay-Konto einloggen oder ein neues OpenPay-Konto eröffnen und die Zahlung autorisieren. Sobald Sie Ihre Daten für die Zahlung bestätigt haben, werden Sie automatisch wieder zurück in den Shop geleitet, um die Bestellung abzuschließen.</div> <div style="margin-top: 5px">Erst dann wird die Zahlung ausgeführt.</div>'
        );

        $payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if (!$payment->load('openpaycredit')) {
            $payment->setId('openpaycredit');
            $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
            $payment->oxpayments__oxdesc = new \OxidEsales\Eshop\Core\Field('OpenPay Credit Card');
            $payment->oxpayments__oxaddsum = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxaddsumtype = new \OxidEsales\Eshop\Core\Field('abs');
            $payment->oxpayments__oxfromboni = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxfromamount = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxfromamount = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxtoamount = new \OxidEsales\Eshop\Core\Field(10000);

            $language = \OxidEsales\Eshop\Core\Registry::getLang();
            $languages = $language->getLanguageIds();
            foreach ($paymentDescriptionsCredit as $languageAbbreviation => $description) {
                $languageId = array_search($languageAbbreviation, $languages);
                if ($languageId !== false) {
                    $payment->setLanguage($languageId);
                    $payment->oxpayments__oxlongdesc = new \OxidEsales\Eshop\Core\Field($description);
                    $payment->save();
                }
            }
        }

        if (!$payment->load('openpaydebit')) {
            $payment->setId('openpaydebit');
            $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
            $payment->oxpayments__oxdesc = new \OxidEsales\Eshop\Core\Field('OpenPay Debit Card');
            $payment->oxpayments__oxaddsum = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxaddsumtype = new \OxidEsales\Eshop\Core\Field('abs');
            $payment->oxpayments__oxfromboni = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxfromamount = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxtoamount = new \OxidEsales\Eshop\Core\Field(10000);

            $language = \OxidEsales\Eshop\Core\Registry::getLang();
            $languages = $language->getLanguageIds();
            foreach ($paymentDescriptionsDebit as $languageAbbreviation => $description) {
                $languageId = array_search($languageAbbreviation, $languages);
                if ($languageId !== false) {
                    $payment->setLanguage($languageId);
                    $payment->oxpayments__oxlongdesc = new \OxidEsales\Eshop\Core\Field($description);
                    $payment->save();
                }
            }
        }

        if (!$payment->load('openpaystores')) {
            $payment->setId('openpaystores');
            $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
            $payment->oxpayments__oxdesc = new \OxidEsales\Eshop\Core\Field('OpenPay Pay on Stores');
            $payment->oxpayments__oxaddsum = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxaddsumtype = new \OxidEsales\Eshop\Core\Field('abs');
            $payment->oxpayments__oxfromboni = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxfromamount = new \OxidEsales\Eshop\Core\Field(0);
            $payment->oxpayments__oxtoamount = new \OxidEsales\Eshop\Core\Field(10000);

            $language = \OxidEsales\Eshop\Core\Registry::getLang();
            $languages = $language->getLanguageIds();
            foreach ($paymentDescriptionsStores as $languageAbbreviation => $description) {
                $languageId = array_search($languageAbbreviation, $languages);
                if ($languageId !== false) {
                    $payment->setLanguage($languageId);
                    $payment->oxpayments__oxlongdesc = new \OxidEsales\Eshop\Core\Field($description);
                    $payment->save();
                }
            }
        }
    }

    /**
     * Check if OpenPay is used for sub-shops.
     *
     * @return bool
     */
//    public static function isOpenPayActiveOnSubShops()
//    {
//        $active = false;
//        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
//        $extensionChecker = oxNew(\OxidEsales\OpenPayModule\Core\ExtensionChecker::class);
//        $shops = $config->getShopIds();
//        $activeShopId = $config->getShopId();
//
//        foreach ($shops as $shopId) {
//            if ($shopId != $activeShopId) {
//                $extensionChecker->setShopId($shopId);
//                $extensionChecker->setExtensionId('weeopenpay');
//                if ($extensionChecker->isActive()) {
//                    $active = true;
//                    break;
//                }
//            }
//        }
//
//        return $active;
//    }

    /**
     * Disables OpenPay Credit Card payment method
     */
    public static function disableCreditPaymentMethod()
    {
        $payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $payment->load('openpaycredit');
        $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(0);
        $payment->save();
    }

    /**
     * Activates OpenPay Credit Card payment method
     */
    public static function enableCreditPaymentMethod()
    {
        $payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $payment->load('openpaycredit');
        $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
        $payment->save();
    }

    /**
     * Disables OpenPay Debit Card payment method
     */
    public static function disableDebitPaymentMethod()
    {
        $payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $payment->load('openpaydebit');
        $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(0);
        $payment->save();
    }

    /**
     * Activates OpenPay Debit Card payment method
     */
    public static function enableDebitPaymentMethod()
    {
        $payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $payment->load('openpaydebit');
        $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
        $payment->save();
    }

    /**
     * Disables OpenPay Stores payment method
     */
    public static function disableStoresPaymentMethod()
    {
        $payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $payment->load('openpaystores');
        $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(0);
        $payment->save();
    }

    /**
     * Activates OpenPay Stores payment method
     */
    public static function enableStoresPaymentMethod()
    {
        $payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        $payment->load('openpaystores');
        $payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
        $payment->save();
    }

    /**
     * Creates Order payments table in to database if not exist
     */
    public static function addOrderPaymentsTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `weeopenpay_orderpayments` (
              `WEEOPENPAY_PAYMENTID` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `WEEOPENPAY_ACTION` enum('capture', 'authorization', 're-authorization', 'refund', 'void') NOT NULL DEFAULT 'capture',
              `WEEOPENPAY_ORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
              `WEEOPENPAY_TRANSACTIONID` varchar(32) NOT NULL,
              `WEEOPENPAY_CORRELATIONID` varchar(32) NOT NULL,
              `WEEOPENPAY_AMOUNT` decimal(9,2) NOT NULL,
              `WEEOPENPAY_CURRENCY` varchar(3) NOT NULL,
              `WEEOPENPAY_REFUNDEDAMOUNT` decimal(9,2) NOT NULL,
              `WEEOPENPAY_DATE` datetime NOT NULL,
              `WEEOPENPAY_STATUS` varchar(20) NOT NULL,
              `WEEOPENPAY_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              PRIMARY KEY (`WEEOPENPAY_PAYMENTID`),
              KEY `WEEOPENPAY_ORDERID` (`WEEOPENPAY_ORDERID`),
              KEY `WEEOPENPAY_DATE` (`WEEOPENPAY_DATE`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Creates Order payments Comments table in to database if not exist
     */
    public static function addOrderPaymentsCommentsTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `weeopenpay_orderpaymentcomments` (
              `WEEOPENPAY_COMMENTID` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `WEEOPENPAY_PAYMENTID` int(11) unsigned NOT NULL,
              `WEEOPENPAY_COMMENT` varchar(256) NOT NULL,
              `WEEOPENPAY_DATE` datetime NOT NULL,
              `WEEOPENPAY_TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
              PRIMARY KEY (`WEEOPENPAY_COMMENTID`),
              KEY `WEEOPENPAY_ORDERID` (`WEEOPENPAY_PAYMENTID`),
              KEY `WEEOPENPAY_DATE` (`WEEOPENPAY_DATE`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Enables OpenPay RDF
     *
     * @return null
     */
    public static function enableOpenPayRDFA()
    {
        // If OpenPay activated on other sub shops do not change global RDF setting.
        if ('EE' == \OxidEsales\Eshop\Core\Registry::getConfig()->getEdition() && self::isOpenPayActiveOnSubShops()) {
            return;
        }

        $query = "INSERT IGNORE INTO `oxobject2payment` (`OXID`, `OXPAYMENTID`, `OXOBJECTID`, `OXTYPE`) VALUES('weeopenpayrdfa', 'weeopenpay', 'OpenPay', 'rdfapayment')";
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Disable OpenPay RDF
     */
    public static function disableOpenPayRDFA()
    {
        $query = "DELETE FROM `oxobject2payment` WHERE `OXID` = 'weeopenpayrdfa'";

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Add missing field if it activates on old DB
     */
    public static function addMissingFieldsOnUpdate()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);

        $tableFields = array(
            'weeopenpay_order'                => 'WEEOPENPAY_TIMESTAMP',
            'weeopenpay_orderpayments'        => 'WEEOPENPAY_TIMESTAMP',
            'weeopenpay_orderpaymentcomments' => 'WEEOPENPAY_TIMESTAMP',
        );

        foreach ($tableFields as $tableName => $fieldName) {
            if (!$dbMetaDataHandler->fieldExists($fieldName, $tableName)) {
                \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                    "ALTER TABLE `" . $tableName
                    . "` ADD `" . $fieldName . "` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;"
                );
            }
        }
    }

    /**
     * Update tables and its fields encoding/collation if activated on old DB
     */
    public static function ensureCorrectFieldsEncodingOnUpdate()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
        if ($dbMetaDataHandler->tableExists("weeopenpay_order")) {
            $query = "ALTER TABLE `weeopenpay_order` DEFAULT CHARACTER SET utf8 collate utf8_general_ci;";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

            $query = "ALTER TABLE `weeopenpay_orderpaymentcomments` DEFAULT CHARACTER SET utf8 collate utf8_general_ci;";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

            $query = "ALTER TABLE `weeopenpay_orderpayments`  DEFAULT CHARACTER SET utf8 collate utf8_general_ci;";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

            $query = "ALTER TABLE `weeopenpay_order` 
              MODIFY `WEEOPENPAY_CURRENCY` varchar(32) character set utf8 collate utf8_general_ci NOT NULL,
              MODIFY `WEEOPENPAY_PAYMENTSTATUS` enum('pending','completed','failed','canceled') CHARACTER SET utf8 collate utf8_general_ci NOT NULL DEFAULT 'pending',
              MODIFY `WEEOPENPAY_TRANSACTIONMODE` enum('Sale','Authorization') CHARACTER SET utf8 collate utf8_general_ci NOT NULL DEFAULT 'Sale';";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

            $query = "ALTER TABLE `weeopenpay_orderpaymentcomments` 
              MODIFY `WEEOPENPAY_COMMENT` varchar(256) character set utf8 collate utf8_general_ci NOT NULL;";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

            $query = "ALTER TABLE `weeopenpay_orderpayments` 
                MODIFY `WEEOPENPAY_ACTION` enum('capture','authorization','re-authorization','refund','void') CHARACTER SET utf8 collate utf8_general_ci NOT NULL DEFAULT 'capture',
                MODIFY `WEEOPENPAY_TRANSACTIONID` varchar(32) character set utf8 collate utf8_general_ci NOT NULL,
                MODIFY `WEEOPENPAY_CORRELATIONID` varchar(32) character set utf8 collate utf8_general_ci NOT NULL,
                MODIFY `WEEOPENPAY_CURRENCY` varchar(3) character set utf8 collate utf8_general_ci NOT NULL,
                MODIFY `WEEOPENPAY_STATUS` varchar(20) character set utf8 collate utf8_general_ci NOT NULL;";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
        }
    }

    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        // add additional field to order
        self::addOrderTable();

        // create orders payments table
        self::addOrderPaymentsTable();

        // payment comments
        self::addOrderPaymentsCommentsTable();

        self::addMissingFieldsOnUpdate();
        self::ensureCorrectFieldsEncodingOnUpdate();

        // adding record to oxPayment table
        self::addPaymentMethod();

        // enabling OpenPay Credit Card payment method
        self::enableCreditPaymentMethod();

        // enabling OpenPay Debit Card payment method
        self::enableDebitPaymentMethod();

        // enabling OpenPay Stores payment method
        self::enableStoresPaymentMethod();

        // enable OpenPay RDF
        self::enableOpenPayRDFA();
    }

    /**
     * Delete the basket object, which is saved in the session, as it is an instance of \OxidEsales\OpenPayModule\Model\Basket
     * and it is no longer a valid object after the module has been deactivated.
     */
    public static function deleteSessionBasket()
    {
        \OxidEsales\Eshop\Core\Registry::getSession()->delBasket();
    }

    /**
     * Execute action on deactivate event
     *
     * @return null
     */
    public static function onDeactivate()
    {
        // If OpenPay activated on other sub shops do not remove payment method and RDF setting
        if ('EE' == \OxidEsales\Eshop\Core\Registry::getConfig()->getEdition() && self::isOpenPayActiveOnSubShops()) {
            return;
        }
        self::disableCreditPaymentMethod();
        self::disableDebitPaymentMethod();
        self::disableStoresPaymentMethod();
        self::disableOpenPayRDFA();
        self::deleteSessionBasket();
    }
}
