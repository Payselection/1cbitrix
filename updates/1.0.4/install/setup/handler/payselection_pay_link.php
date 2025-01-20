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

    $request = $service->initiatePay($payment, $context->getRequest());;
    $paymentURL = $request->getPaymentUrl();

    /**
     * TODO this is better way, but it doesn't work locally
     */
//    $eventName = "SEND";
//    $eventMessage = new CEventMessage;
//    $arFields = array(
//        "ACTIVE" => "Y",
//        "EVENT_NAME" => $eventName,
//        "LID" => "s1",
//        "EMAIL_TO" => $email,
//        "SUBJECT" => "������ �� ������",
//        "MESSAGE" => '
//            <div class="mb-4">
//                <p>������ ������������� ������ <b>&laquo;Payselection&raquo;</b>.</p>
//                <p>����� � ������: </p>
//                <div class="d-flex align-items-center mb-3">
//                    <div class="col-auto pl-0">
//                        <a class="btn btn-lg btn-success pl-4 pr-4" style="border-radius: 32px;" href="' . $paymentURL . '">��������</a>
//                    </div>
//                    <div class="col pr-0">&nbsp;&nbsp;�� ������ �������������� �� �������� ������</div>
//                </div>
//            </div>
//        ',
//        "BODY_TYPE" => "html"
//    );
//
//    $eventMessage->Add($arFields);
//
//    $result = \Bitrix\Main\Mail\Event::send(array(
//        "EVENT_NAME" => "SEND",
//        "LID" => "s1",
//        "C_FIELDS" => $arFields,
//
//    ));
//    if ($result->isSuccess()) {
//        echo "������ �� ������ ���������� �� ����� $email.";
//    } else {
//        $errors = $result->getErrorMessages();
//        echo "������ ��� �������� ������: " . implode(", ", $errors);
//    }

    $to = $email;
    $subject = "������ �� ������";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message = '<html><body>';
    $message = '
            <div class="mb-4">
                <p>������ ������������� ������ <b>&laquo;Payselection&raquo;</b>.</p>
                <p>����� � ������: </p>
                <div class="d-flex align-items-center mb-3">
                    <div class="col-auto pl-0">
                        <a class="btn btn-lg btn-success pl-4 pr-4" style="border-radius: 32px;" href="' . $paymentURL . '">��������</a>
                    </div>
                    <div class="col pr-0">&nbsp;&nbsp;�� ������ �������������� �� �������� ������</div>
                </div>
            </div>
        ';
    $message .= '</body></html>';
    if (bxmail($to, $subject, $message, $headers)) {
        echo "������ �� ������ ���������� �� ����� $email.";
    } else {
        echo "������ ��� �������� ������";
    }
}


?>
