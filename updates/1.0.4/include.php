<?php
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

\Bitrix\Main\Loader::registerAutoLoadClasses("p10102022_p10102022paycode2022", array());

class PayselectionButton {
    public static function OrderDetailAdminContextMenuShow_(&$items){
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && in_array($GLOBALS['APPLICATION']->GetCurPage(), ['/bitrix/admin/sale_order_edit.php', '/bitrix/admin/sale_order_view.php']) && $_REQUEST['ID'] > 0) {
            $items[] = array(
                "TEXT" => "Отправить ссылку",
                "LINK" => "javascript:payselection_pay_link(" . $_REQUEST['ID'] . ")",
                "TITLE" => "Отправить ссылку",
                "ICON" => "adm-btn",
            );
        }
    }
}
