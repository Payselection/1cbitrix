<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Internal\EventTypeTable;
use Bitrix\Main\Mail\Internal\EventMessageTable;
use Bitrix\Main\Mail\Internal\EventMessageSiteTable;
use Bitrix\Main\SiteTable;

Loc::loadMessages(__FILE__);
IncludeModuleLangFile(__FILE__);
if (class_exists('p10102022_p10102022paycode2022')) return;

class p10102022_p10102022paycode2022 extends CModule
{

    var $MODULE_ID = "p10102022.p10102022paycode2022";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = Loc::getMessage("MODULE_PAYSELECTION_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_PAYSELECTION_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("MODULE_PAYSELECTION_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("MODULE_PAYSELECTION_PARTNER_URI");

        $ps_dir_path = strlen(COption::GetOptionString('sale', 'path2user_ps_files')) > 3 ? COption::GetOptionString('sale', 'path2user_ps_files') : '/bitrix/php_interface/include/sale_payment/';
        $this->PAYMENT_HANDLER_PATH = $_SERVER["DOCUMENT_ROOT"] . $ps_dir_path . str_replace(".", "_", $this->MODULE_ID) . "/";
    }

    public function DoInstall()
    {
        $this->installFiles();
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        // Устанавливаем почтовое событие
        $this->installMailEvents();
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnAdminContextMenuShow',
            $this->MODULE_ID,
            'CustomOrderHandler',
            'onAdminContextMenuShowHandler'
        );
        return true;
    }

    public function DoUninstall()
    {
        // Удаляем почтовые события
        $this->uninstallMailEvents();
        // Удаляем обработчики событий
        $this->UnInstallEvents();
        $this->uninstallFiles();
        \Bitrix\Main\Config\Option::delete($this->MODULE_ID);
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        return true;
    }

    public function installFiles()
    {
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/setup/images/logo",
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/sale/sale_payments/'
        );
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/setup/handler",
            $this->PAYMENT_HANDLER_PATH,
            true, true
        );

        return true;
    }

    public function uninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/p10102022_p10102022paycode2022");
        return true;
    }

    private function installMailEvents()
    {
        // Получаем список сайтов
        $sites = [];
        $result = SiteTable::getList([
            'filter' => ['ACTIVE' => 'Y'],
            'select' => ['LID'],
        ]);

        while ($site = $result->fetch()) {
            $sites[] = $site['LID'];
        }

        if (empty($sites)) {
            throw new \RuntimeException('Нет активных сайтов для привязки почтового шаблона.');
        }

        // Добавляем почтовое событие
        $eventTypeResult = EventTypeTable::add([
            'LID'         => 'ru',
            'EVENT_NAME'  => 'PAYSELECTION_ORDER_SEND_LINK',
            'EVENT_TYPE'  => 'email',
            'NAME'        => 'Ссылка на оплату заказа',
            'DESCRIPTION' => "#EMAIL_TO# - Email получателя\n#ORDER_ID# - Номер заказа\n#ORDER_SUM# - Сумма заказа",
        ]);
        if (!$eventTypeResult->isSuccess()) {
            throw new \Bitrix\Main\SystemException("Ошибка создания почтового события");
        }
        $eventTypeResult = EventTypeTable::add([
            'LID'         => 'en',
            'EVENT_NAME'  => 'PAYSELECTION_ORDER_SEND_LINK',
            'EVENT_TYPE'  => 'email',
            'NAME'        => 'Link to order payment',
            'DESCRIPTION' => "#EMAIL_TO# - Email получателя\n#ORDER_ID# - Номер заказа\n#ORDER_SUM# - Сумма заказа",
        ]);
        if (!$eventTypeResult->isSuccess()) {
            throw new \Bitrix\Main\SystemException("Ошибка создания почтового события");
        }

        // Добавляем почтовый шаблон для всех сайтов
        $message = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/templates/order_send_link.html");
        foreach ($sites as $siteId) {
            $eventMessageResult = EventMessageTable::add([
                'ACTIVE' => 'Y',
                'EVENT_NAME' => 'PAYSELECTION_ORDER_SEND_LINK',
                'LID' => $siteId,
                'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                'EMAIL_TO' => '#EMAIL_TO#',
                'SUBJECT' => 'Ссылка на оплату заказа №#ORDER_ID#',
                'MESSAGE' => $message,
                'BODY_TYPE' => 'html',
            ]);
            if (!$eventMessageResult->isSuccess()) {
                throw new \Bitrix\Main\SystemException("Ошибка создания шаблона письма");
            }
            $eventMessageId = $eventMessageResult->getId();
            $siteBindingResult = EventMessageSiteTable::add([
                'EVENT_MESSAGE_ID' => $eventMessageId,
                'SITE_ID' => $siteId,
            ]);

            if (!$siteBindingResult->isSuccess()) {
                throw new \RuntimeException('Ошибка при привязке шаблона к сайту ' . $siteId . ': ' . implode(', ', $siteBindingResult->getErrorMessages()));
            }
        }
    }

    public function UnInstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnAdminContextMenuShow',
            $this->MODULE_ID,
            '\CustomOrderHandler',
            'onAdminContextMenuShowHandler'
        );

        return true;
    }

    private function uninstallMailEvents()
    {
        // Удаляем почтовое событие
        $eventTypes = EventTypeTable::getList([
            'filter' => ['EVENT_NAME' => 'PAYSELECTION_ORDER_SEND_LINK'],
        ]);

        while ($eventType = $eventTypes->fetch()) {
            EventTypeTable::delete($eventType['ID']);
        }

        // Удаляем почтовый шаблон
        $eventMessages = EventMessageTable::getList([
            'filter' => ['EVENT_NAME' => 'PAYSELECTION_ORDER_SEND_LINK'],
        ]);
        while ($eventMessage = $eventMessages->fetch()) {
            $eventMessageId = $eventMessage['ID'];
            $eventMessageSites = EventMessageSiteTable::getList([
                'filter' => ['EVENT_MESSAGE_ID' => $eventMessageId],
            ]);

            while ($eventMessageSite = $eventMessageSites->fetch()) {
                EventMessageSiteTable::delete([
                    'EVENT_MESSAGE_ID' => $eventMessageSite['EVENT_MESSAGE_ID'],
                    'SITE_ID' => $eventMessageSite['SITE_ID'],
                ]);
            }

            EventMessageTable::delete($eventMessageId);
        }
    }
}
