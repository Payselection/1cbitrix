<?php
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;

class CustomOrderHandler
{
    /**
     * Обработчик события OnAdminContextMenuShow
     */
    public static function onAdminContextMenuShowHandler(&$items)
    {
        global $APPLICATION;
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !in_array($APPLICATION->GetCurPage(), ['/bitrix/admin/sale_order_edit.php', '/bitrix/admin/sale_order_view.php'])) {
            return;
        }

        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        if ($orderId = $request->get('ID')) {
            Loader::includeModule('sale');
            $order = Order::load((int)$orderId);

            if ($order) {
                $email = $order->getPropertyCollection()->getUserEmail()->getValue();
                $price = $order->getPrice();
                $currency = $order->getCurrency();

                if ($request->get('send_payment_link') === 'Y') {
                    try {
                        $paymentCollection = $order->getPaymentCollection();
                        $payment = $paymentCollection[0];
                        $service = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
                        $context = \Bitrix\Main\Application::getInstance()->getContext();
                        $req = $service->initiatePay($payment, $context->getRequest());
                        $paymentURL = $req->getPaymentUrl();

                        if (empty($paymentURL)) {
                            throw new \Exception("Ошибка платежной системы");
                        }

                        $result = Event::sendImmediate([
                            'EVENT_NAME' => 'PAYSELECTION_ORDER_SEND_LINK',
                            'LID' => 's1',
                            'C_FIELDS' => [
                                'EMAIL_TO' => $email,
                                'ORDER_ID' => $orderId,
                                'ORDER_SUM' => SaleFormatCurrency($price, $currency),
                                'PAYMENT_URL' => $paymentURL,
                            ],
                        ]);

                        if ($result === 'Y') {
                            LocalRedirect($APPLICATION->GetCurPageParam("link_sent=Y", ["send_payment_link"]));
                        } else {
                            LocalRedirect($APPLICATION->GetCurPageParam("link_sent=N", ["send_payment_link"]));
                        }
                    } catch (\TypeError $e) {
                        \CAdminMessage::ShowMessage([
                            "MESSAGE" => "Ошибка при получении URL оплаты: {$e->getMessage()}",
                            "TYPE" => "ERROR",
                        ]);
                    } catch (\Exception $e) {
                        \CAdminMessage::ShowMessage([
                            "MESSAGE" => $e->getMessage(),
                            "TYPE" => "ERROR",
                        ]);
                    }
                }
                if ($request->get('link_sent') === 'Y') {
                    \CAdminMessage::ShowMessage([
                        "MESSAGE" => "Ссылка на оплату отправлена на адрес $email.",
                        "TYPE" => "OK",
                    ]);
                }
            }
            // Добавляем кнопку в админку
            $items[] = [
                'TEXT'  => 'Отправить ссылку',
                'LINK'  => "javascript:if(confirm('Отправить ссылку на оплату для заказа №{$orderId}?'))
                        window.location.href='".$APPLICATION->GetCurPageParam('send_payment_link=Y', ['send_payment_link'])."';",
                'TITLE' => 'Отправить ссылку на оплату клиенту',
                'ICON'  => 'btn_send',
            ];
        }
    }
}
