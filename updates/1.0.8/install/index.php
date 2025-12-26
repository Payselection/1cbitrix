<?php

use Bitrix\Main\Localization\Loc;

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
        $this->errors = false;
        $eventManager->registerEventHandlerCompatible('main', 'OnAdminContextMenuShow', $this->MODULE_ID, "PayselectionButton", 'OrderDetailAdminContextMenuShow_',9999);
        return true;
    }

    public function DoUninstall()
    {
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
}
