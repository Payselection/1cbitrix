<?php

use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem\Manager;
use Bitrix\Main\Application;
use Bitrix\Main\Mail\Event;


if (isset($_GET['Order_ID'])) {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    ob_get_clean();

    \Bitrix\Main\Loader::includeModule("sale");
    $order = \Bitrix\Sale\Order::load((int)$_GET['Order_ID']);
    $email = $order->getPropertyCollection()->getUserEmail()->getValue();
    $paymentCollection = $order->getPaymentCollection();
    $payment = $paymentCollection [0];

    $service = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
    $context = \Bitrix\Main\Application::getInstance()->getContext();

    $request = $service->initiatePay($payment, $context->getRequest()); ;


    $eventName = 'SEND_PAYMENT_LINK';
    $siteId = 's1';
    $fields = array(
        'EMAIL' => $email,
        'PAYMENT_LINK' => $request,
    );

    $result = Event::send(array(
        "EVENT_NAME" => $eventName,
        "LID" => SITE_ID,
        "C_FIELDS" => $fields,
    ));

    if ($result->isSuccess()) {
        echo "Ссылка на оплату отправлена на адрес $email.";
    } else {
        echo "Ошибка при отправке ссылки на оплату.";
    }

};

?>
