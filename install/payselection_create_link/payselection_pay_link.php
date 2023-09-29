<?php

use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem\Manager;
use Bitrix\Main\Application;


if (isset($_GET['Order_ID'])) {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    ob_get_clean();

    \Bitrix\Main\Loader::includeModule("sale");
    $order = \Bitrix\Sale\Order::load((int)$_GET['Order_ID']);
    $paymentCollection = $order->getPaymentCollection();
    $payment = $paymentCollection [0];
    $service = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
    $context = \Bitrix\Main\Application::getInstance()->getContext();
    $request2 = $service->initiatePay($payment, $context->getRequest());
    $requestParams = $request2->getTemplateParams($payment);

    $url = 'https://gw.payselection.com/webpayments/paylink_create';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "fLdy7fSenC6KVDKm");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));
    $content = curl_exec($ch);
    curl_close($ch);
    $content = json_decode($content, true);
    if ($content['Success'] == 'true') {
        echo 'Ссылка на оплату отправлена клиенту!';
    } else echo 'Что то пошло не так! Обратитесь к администратору сайта!';

};

?>
