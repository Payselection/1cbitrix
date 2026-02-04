<?

use Bitrix\Main\Mail\Internal\EventTypeTable;
use Bitrix\Main\Mail\Internal\EventMessageTable;
use Bitrix\Main\Mail\Internal\EventMessageSiteTable;
use Bitrix\Main\SiteTable;

$MODULE_ID = "p10102022.p10102022paycode2022";
$updater->CopyFiles("install/setup/handler", "php_interface/include/sale_payment/p10102022_p10102022paycode2022", true, true);
$updater->CopyFiles("templates", "modules/" . $MODULE_ID, true, true);

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

// Добавляем почтовое событие (если ещё не существует)
$existingRu = EventTypeTable::getList([
    'filter' => ['EVENT_NAME' => 'PAYSELECTION_ORDER_SEND_LINK', 'LID' => 'ru'],
    'select' => ['ID'],
    'limit'  => 1,
])->fetch();

if (!$existingRu) {
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
}

$existingEn = EventTypeTable::getList([
    'filter' => ['EVENT_NAME' => 'PAYSELECTION_ORDER_SEND_LINK', 'LID' => 'en'],
    'select' => ['ID'],
    'limit'  => 1,
])->fetch();

if (!$existingEn) {
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
}

// Добавляем почтовый шаблон для всех сайтов
$message = '<style>
    body
    {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 14px;
        color: #000;
    }
</style>
<table cellpadding="0" cellspacing="0" width="850" style="background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;" border="1" bordercolor="#d1d1d1">
    <tbody>
    <tr>
        <td height="83" width="850" bgcolor="#eaf3f5" style="border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tbody>
                <tr>
                    <td bgcolor="#ffffff" height="75" style="font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;">
                        <a style="color:#2e6eb6;" href="#PAYMENT_URL#">Ссылка на заказ</a>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#bad3df" height="11">
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;">
            <p style="margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;">
                Ссылка на оплату заказа №#ORDER_ID#,
            </p>
            <p>
                Услугу предоставляет сервис <b>«Payselection»</b>.
            </p>
            <p>
                Ваш заказ №#ORDER_ID#
            </p>
            <p>
                Сумма к оплате: <b>#ORDER_SUM#</b>
            </p>
            <p>
                <a style="color:#2e6eb6;" href="#PAYMENT_URL#">Оплатить</a>
            </p>
            <p>
                Вы будете перенаправлены на страницу оплаты
            </p>
        </td>
    </tr>
    <tr>
        <td height="40px" width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;">
            <p style="border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;">
                С уважением,<br>
                администрация <a href="http://#SERVER_NAME#" style="color:#2e6eb6;">Интернет-магазина</a><br>
            </p>
        </td>
    </tr>
    </tbody>
</table>';
foreach ($sites as $siteId) {
    $existingMessage = EventMessageTable::getList([
        'filter' => ['EVENT_NAME' => 'PAYSELECTION_ORDER_SEND_LINK', 'LID' => $siteId],
        'select' => ['ID'],
        'limit'  => 1,
    ])->fetch();

    if (!$existingMessage) {
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

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->registerEventHandlerCompatible(
    'main',
    'OnAdminContextMenuShow',
    $MODULE_ID,
    'CustomOrderHandler',
    'onAdminContextMenuShowHandler'
);