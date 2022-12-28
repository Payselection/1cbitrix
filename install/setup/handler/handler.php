<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main,
    Bitrix\Main\Web\HttpClient,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type\DateTime,
    Bitrix\Sale,
    Bitrix\Sale\PaySystem,
    Bitrix\Main\Request,
    Bitrix\Sale\Payment,
    Bitrix\Sale\Order,
    Bitrix\Sale\Cashbox,
    Bitrix\Sale\PaySystem\ServiceResult,
    Bitrix\Sale\PaymentCollection,
    Bitrix\Sale\PriceMaths;

Loc::loadMessages(__FILE__);

/**
 * Class PayselectionHandler
 * @package Sale\Handlers\PaySystem
 */
class p10102022_p10102022paycode2022Handler extends PaySystem\ServiceHandler implements PaySystem\IRefund
{
    private const MODE_CHECKOUT = 'checkout';
    private const MODE_WIDGET = 'widget';

    private const TRACKING_ID_DELIMITER = '#';

    private const STATUS_SUCCESSFUL_CODE = 'success';
    private const STATUS_PREAUTORIZED_CODE = 'preauthorized';
    private const STATUS_VOIDED_CODE = 'voided';
    private const STATUS_ERROR_CODE = 'error';

    private const SEND_METHOD_HTTP_POST = "POST";
    private const SEND_METHOD_HTTP_GET = "GET";

    /**
     * @return array
     */
    public static function getHandlerModeList(): array
    {
        return array(
            static::MODE_CHECKOUT => Loc::getMessage('SALE_PAYSELECTION_CHECKOUT_MODE'),
            static::MODE_WIDGET => Loc::getMessage('SALE_PAYSELECTION_WIDGET_MODE'),
        );
    }

    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return ServiceResult
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function initiatePay(Payment $payment, Request $request = null): ServiceResult
    {
        $result = new ServiceResult();

        if ($this->isCheckoutMode()) {
            $createPaymentTokenResult = $this->createPaymentToken($payment);
            if (!$createPaymentTokenResult->isSuccess()) {
                $result->addErrors($createPaymentTokenResult->getErrors());
                return $result;
            }

            $createPaymentTokenData = $createPaymentTokenResult->getData();
            $result->setPaymentUrl($createPaymentTokenData['url']);
            $this->setExtraParams($createPaymentTokenData);
        } else if ($this->isWidgetMode()) {
            $t = $this->getTemplateParams($payment);
            PaySystem\Logger::addDebugInfo(__CLASS__ . ':getTemplateParams: ' . static::encode($t));
            $this->setExtraParams($t);
        } else {
            return $result;
        }

        $showTemplateResult = $this->showTemplate($payment, $this->getTemplateName());
        if ($showTemplateResult->isSuccess()) {
            $result->setTemplate($showTemplateResult->getTemplate());
        } else {
            $result->addErrors($showTemplateResult->getErrors());
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getTemplateName(): string
    {
        return (string)$this->service->getField('PS_MODE');
    }

    /**
     * @param Payment $payment
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    private function getTemplateParams(Payment $payment): array
    {
        $orderId = $payment->getId() . self::TRACKING_ID_DELIMITER . $this->service->getField('ID');
        $params = [
            'ServiceId' => $this->getBusinessValue($payment, 'PAYSELECTION_SITE_ID'),
            'Key' => $this->getBusinessValue($payment, 'PAYSELECTION_KEY'),
            'WidgetUrl' => $this->getBusinessValue($payment, 'PAYSELECTION_WIDGET_API_URL'),
            'PaymentRequest' => [
                'Amount' => (string)($this->roundNumber($payment->getSum())),
                'Currency' => $payment->getField('CURRENCY'),
                'Description' => $this->getPaymentDescription($payment),
                'OrderId' => $orderId,
                'ExtraData' => [
                    'WebhookUrl' => $this->getNotificationUrl($payment),
                    'SuccessUrl' => $this->getSuccessUrl($payment),
                    'DeclineUrl' => $this->getDeclineUrl($payment),
                    'FailUrl' => $this->getFailUrl($payment),
                    'CancelUrl' => $this->getCancelUrl($payment),
                ],
            ],
            'CustomerInfo' => [
                'Language' => LANGUAGE_ID,
            ],
        ];
        if ($this->getBusinessValue($payment, 'PAYSELECTION_RECEIPT')  == 'Y') {
            $params['ReceiptData'] = $this->getReceiptData($payment);
        }
        $params['sum'] = (string)($this->roundNumber($payment->getSum()));
        $params['currency'] = $payment->getField('CURRENCY');
        $params['payment_type'] = ($this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_TYPE_SYSTEM') === '1' ? 'Block' : 'Pay');

        return $params;
    }

    /**
     * @param Payment $payment
     * @return ServiceResult
     * @throws Main\ArgumentException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    private function createPaymentToken(Payment $payment): ServiceResult
    {
        $result = new ServiceResult();
        $url = $this->getUrl($payment, 'getPaymentCreate');
        $orderId = $payment->getId() . self::TRACKING_ID_DELIMITER . $this->service->getField('ID');
        $params = [
            'MetaData' => [
                'PaymentType' => ($this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_TYPE_SYSTEM') === '1' ? 'Block' : 'Pay'),
            ],
            'PaymentRequest' => [
                'Amount' => (string)($this->roundNumber($payment->getSum())),
                'Currency' => $payment->getField('CURRENCY'),
                'Description' => $this->getPaymentDescription($payment),
                'PaymentMethod' => 'Card',
                'RebillFlag' => false,
                'OrderId' => $orderId,
                'ExtraData' => [
                    'WebhookUrl' => $this->getNotificationUrl($payment),
                    'SuccessUrl' => $this->getSuccessUrl($payment),
                    'DeclineUrl' => $this->getDeclineUrl($payment),
                    'FailUrl' => $this->getFailUrl($payment),
                    'CancelUrl' => $this->getCancelUrl($payment),
                ],
            ],
            'CustomerInfo' => [
                'Language' => LANGUAGE_ID,
            ],
        ];
        if ($this->getBusinessValue($payment, 'PAYSELECTION_RECEIPT')  == 'Y') {
            $params['ReceiptData'] = $this->getReceiptData($payment);
        }
        $postData = static::encode($params);
        $headers = $this->getHeaders($payment, $postData, $url);

        $sendResult = $this->sendCreate(self::SEND_METHOD_HTTP_POST, $url, $postData, $headers);
        if ($sendResult->isSuccess()) {
            $result->setData($sendResult->getData());
        } else {
            $result->addErrors($sendResult->getErrors());
        }

        return $result;
    }


    private function getReceiptData(Payment $payment): array
    {
        $collection = $payment->getCollection();
        $order = $collection->getOrder();
        $userEmail = $order->getPropertyCollection()->getUserEmail();
        $orderId = $payment->getId() . self::TRACKING_ID_DELIMITER . $this->service->getField('ID');
        return [
            'timestamp' => date('d.m.Y H:i:s'),
            'external_id' => $orderId,
            'receipt' => [
                'client' => [
                    'email' => (string)(($userEmail) ? $userEmail->getValue() : ''),
                ],
                'company' => [
                    'email' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_EMAIL'),
                    'inn' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_INN'),
                    'sno' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_TAX'),
                    'payment_address' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_ADDRESS'),
                ],
                'items' => $this->setFFDParams($payment),
                'payments' => array([
                    'type' => 1,
                    'sum' => (float)(PriceMaths::roundPrecision($payment->getSum())),
                ]),
                'total' => (float)(PriceMaths::roundPrecision($payment->getSum())),
            ]
        ];
    }


    private function setFFDParams(Payment $payment): array
    {
        $paymentCollection = $payment->getCollection();
        $order = $paymentCollection->getOrder();

        $Basket = $order->getBasket();
        $basketItems = $Basket->getBasketItems();
        $positions = [];

        foreach ($basketItems as $key => $BasketItem) {
            $positions[] = array(
                'name' => str_replace("\n", "", mb_substr($BasketItem->getField('NAME'), 0, 120)),
                'sum' => $BasketItem->getFinalPrice(),
                'price' => $BasketItem->getPrice(),
                'quantity' =>  $BasketItem->getQuantity(),
                'payment_method' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_METHOD'),
                'payment_object' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_OBJECT'),
                'vat' => [
                    'type' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_NDS'),
                ]
            );
        }

        if ($order->getField('PRICE_DELIVERY') > 0) {
            $positions[] = array(
                'name' => Loc::getMessage('SALE_PAYSELECTION_FIRLD_DELIVERY'),
                'sum' => $order->getField('PRICE_DELIVERY'),
                'price' => $order->getField('PRICE_DELIVERY'),
                'quantity' => 1,
                'payment_method' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_METHOD_DELIVERY'),
                'payment_object' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_OBJECT_DELIVERY'),
                'vat' => [
                    'type' => (string)$this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_NDS'),
                ]
            );
        }

        return $positions;
    }


    /**
     * @param Payment $payment
     * @return ServiceResult
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectException
     */
    private function getPayselectionPayment(Payment $payment): ServiceResult
    {
        $result = new ServiceResult();

        $url = $this->getUrl($payment, 'getPaymentStatus');
        $headers = $this->getHeaders($payment, '', $url, 'GET');
        PaySystem\Logger::addDebugInfo(__CLASS__ . ':getPayselectionPayment url: ' . $url);

        $sendResult = $this->send(self::SEND_METHOD_HTTP_GET, $url, [], $headers);
        if ($sendResult->isSuccess()) {
            $paymentData = $sendResult->getData();
            $result->setData($paymentData);
        } else {
            $result->addErrors($sendResult->getErrors());
        }

        return $result;
    }

    /**
     * @param Payment $payment
     * @param $refundableSum
     * @return ServiceResult
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectException
     */
    public function refund(Payment $payment, $refundableSum): ServiceResult
    {
        $result = new ServiceResult();
        $type_system = ($this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_TYPE_SYSTEM') === '1' ? 'cancel' : 'refund');
        $payselectionPaymentResult = $this->getPayselectionPayment($payment);
        if ($payselectionPaymentResult->isSuccess())
        {
            $payselectionPaymentData = $payselectionPaymentResult->getData();
            if (!empty($payselectionPaymentData['Code'])) {
                $result->addError(
                    PaySystem\Error::create(
                        Loc::getMessage('SALE_PAYSELECTION_ERROR_STATUS',
                            [
                                '#STATUS#' => $payselectionPaymentData['Description'],
                            ]
                        )
                    )
                );
            } else
            {
                switch ($payselectionPaymentData['TransactionState'])
                {
                    case self::STATUS_SUCCESSFUL_CODE:
                        $type_system = 'refund';
                        break;
                    case self::STATUS_PREAUTORIZED_CODE:
                        $type_sytem = 'cancel';
                        break;
                    case self::STATUS_VOIDED_CODE:
                        $result->addError(
                            PaySystem\Error::create(
                                'Transaction already canceled'
                            )
                        );
                        break;
                }
            }
        } else
        {
            $result->addErrors($sendResult->getErrors());
            return $result;
        }
        PaySystem\Logger::addDebugInfo(__CLASS__ . ':refund: ' . $type_type);
        $url = $this->getUrl($payment, $type_system);
        $params = [
            'TransactionId' => $payment->getField('PS_INVOICE_ID'),
            'Amount' => (string)($this->roundNumber($refundableSum)),
            'Currency' => $payment->getField('CURRENCY'),
            'WebhookUrl' => $this->getNotificationUrl($payment),
        ];
        $postData = static::encode($params);
        $headers = $this->getHeaders($payment, $postData, $url);
        $sendResult = $this->send(self::SEND_METHOD_HTTP_POST, $url, $params, $headers);
        if (!$sendResult->isSuccess())
        {
            $result->addErrors($sendResult->getErrors());
            return $result;
        }

        $refundData = $sendResult->getData();
        $verifyResponseResult = $this->verifyResponse($refundData);
        if ($verifyResponseResult->isSuccess())
        {
            $payment->setField('PS_STATUS_DESCRIPTION', $this->roundNumber($refundableSum));
        }
        else
        {
            $result->addErrors($verifyResponseResult->getErrors());
        }

        return $result;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return ServiceResult
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectException
     */
    private function send(string $method, string $url, array $params = [], array $headers = []): ServiceResult
    {
        $result = new ServiceResult();

        $httpClient = new HttpClient();
        foreach ($headers as $name => $value) {
            $httpClient->setHeader($name, $value);
        }

        if ($method === self::SEND_METHOD_HTTP_GET) {
            $response = $httpClient->get($url);
        } else {
            $postData = null;
            if ($params) {
                $postData = static::encode($params);
            }

            PaySystem\Logger::addDebugInfo(__CLASS__ . ': request data: ' . $postData);

            $response = $httpClient->post($url, $postData);
        }

        if ($response === false) {
            $errors = $httpClient->getError();
            foreach ($errors as $code => $message) {
                $result->addError(PaySystem\Error::create($message, $code));
            }

            return $result;
        }

        PaySystem\Logger::addDebugInfo(__CLASS__ . ': response data: ' . $response);

        $response = static::decode($response);
        if ($response) {
            $result->setData($response);
        } else {
            $result->addError(PaySystem\Error::create(Loc::getMessage('SALE_PAYSELECTION_RESPONSE_DECODE_ERROR')));
        }

        return $result;
    }

    /**
     * @param string $method
     * @param string $url
     * @param string $postData
     * @param array $headers
     * @return ServiceResult
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectException
     */
    private function sendCreate(string $method, string $url, string $postData, array $headers = []): ServiceResult
    {
        $result = new ServiceResult();

        $httpClient = new HttpClient();
        foreach ($headers as $name => $value) {
            $httpClient->setHeader($name, $value);
        }

        if ($method === self::SEND_METHOD_HTTP_GET) {
            $response = $httpClient->get($url);
        } else {
            PaySystem\Logger::addDebugInfo(__CLASS__ . ': request data: ' . $postData);

            $response = $httpClient->post($url, $postData);
        }

        if ($response === false) {
            $errors = $httpClient->getError();
            foreach ($errors as $code => $message) {
                $result->addError(PaySystem\Error::create($message, $code));
            }

            return $result;
        }

        PaySystem\Logger::addDebugInfo(__CLASS__ . ': response data: ' . $response);
        if ($response) {
            $result->setData([
                'url' => trim($response, '"'),
            ]);
        } else {
            $result->addError(PaySystem\Error::create(Loc::getMessage('SALE_PAYSELECTION_RESPONSE_DECODE_ERROR')));
        }

        return $result;
    }

    /**
     * @param array $response
     * @return ServiceResult
     */
    private function verifyResponse(array $response): ServiceResult
    {
        $result = new ServiceResult();

        if (!empty($response['Code']))
        {
            $result->addError(PaySystem\Error::create($response['Description']));
        }

        return $result;
    }

    /**
     * @return array|string[]
     */
    public function getCurrencyList(): array
    {
        return ['USD', 'EUR', 'RUB'];
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return ServiceResult
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectException
     * @throws Main\NotImplementedException
     */
    public function processRequest(Payment $payment, Request $request): ServiceResult
    {
        $result = new ServiceResult();

        $inputStream = static::readFromStream();
        PaySystem\Logger::addDebugInfo(__CLASS__ . ':processRequest input stream: ' . $inputStream);
        $data = static::decode($inputStream);
        if (!isset($data['Event'])) {
            PaySystem\Logger::addDebugInfo(__CLASS__ . ':processRequest:check: ' . $data['OrderId']);
            if ($this->isSumCorrect($payment, $data['Amount'])) {
                PaySystem\Logger::addDebugInfo(
                    __CLASS__ . ': check is done'
                );
            } else {
                PaySystem\Logger::addDebugInfo(
                    __CLASS__ . ': check fail(different sum)'
                );
                CHTTP::SetStatus("403 Forbidden");
                die();
            }
        } else {
            $payment->setField('PS_INVOICE_ID', $data['TransactionId']);
            $payselectionPaymentResult = $this->getPayselectionPayment($payment);
            if ($payselectionPaymentResult->isSuccess()) {
                $payselectionPaymentData = $payselectionPaymentResult->getData();
                if (!empty($payselectionPaymentData['Code'])) {
                    $result->addError(
                        PaySystem\Error::create(
                            Loc::getMessage('SALE_PAYSELECTION_ERROR_STATUS',
                                [
                                    '#STATUS#' => $payselectionPaymentData['Description'],
                                ]
                            )
                        )
                    );
                } else if ($payselectionPaymentData['TransactionState'] === self::STATUS_SUCCESSFUL_CODE ||
                    $payselectionPaymentData['TransactionState'] === self::STATUS_PREAUTORIZED_CODE ||
                    $payselectionPaymentData['TransactionState'] === self::STATUS_VOIDED_CODE) {
                    if ($data['Event'] === 'Refund' || $data['Event'] === 'Cancel') {
                        if (PriceMaths::roundPrecision($payselectionPaymentData['StateDetails']['ProcessingAmount']) === PriceMaths::roundPrecision($payment->getField('PS_STATUS_DESCRIPTION')))
                        {
                            $result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);
                            PaySystem\Logger::addDebugInfo(__CLASS__ . ':processRequest MONEY_LEAVING: ' . $payment->getField('PS_STATUS_DESCRIPTION'));
                        }
                    } else {
                        $description = Loc::getMessage('SALE_PAYSELECTION_TRANSACTION', [
                            '#ID#' => $data['TransactionId'],
                        ]);
                        PaySystem\Logger::addDebugInfo(__CLASS__ . ':processRequest description: ' . $description);
                        $fields = [
                            'PS_STATUS_CODE' => $payselectionPaymentData['TransactionState'],
                            'PS_STATUS_DESCRIPTION' => $description,
                            "PS_STATUS_MESSAGE" => $data['Code'],
                            'PS_SUM' => $data['Amount'],
                            'PS_STATUS' => 'N',
                            'PS_CURRENCY' => $data['Currency'],
                            'PS_RESPONSE_DATE' => new Main\Type\DateTime(),
                            'PS_INVOICE_ID' => $data['TransactionId'],
                            'PS_CARD_NUMBER' => $data['CardMasked'],
                        ];

                        if ($this->isSumCorrect($payment, $data['Amount'])) {
                            $fields['PS_STATUS'] = 'Y';

                            PaySystem\Logger::addDebugInfo(
                                __CLASS__ . ': PS_CHANGE_STATUS_PAY=' . $this->getBusinessValue($payment, 'PS_CHANGE_STATUS_PAY')
                            );

                            if ($this->getBusinessValue($payment, 'PS_CHANGE_STATUS_PAY') === 'Y') {
                                $result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
                            }
                        } else {
                            $error = Loc::getMessage('SALE_PAYSELECTION_ERROR_SUM');
                            $fields['PS_STATUS_DESCRIPTION'] .= '. ' . $error;
                            $result->addError(PaySystem\Error::create($error));
                        }
                        $result->setPsData($fields);
                    }
                } else {
                    $result->addError(
                        PaySystem\Error::create(
                            Loc::getMessage('SALE_PAYSELECTION_ERROR_STATUS',
                                [
                                    '#STATUS#' => $payselectionPaymentData['TransactionState'],
                                ]
                            )
                        )
                    );
                }
            } else {
                $result->addErrors($payselectionPaymentResult->getErrors());
            }
        }

        return $result;
    }

    /**
     * @param Payment $payment
     * @param $sum
     * @return bool
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ArgumentTypeException
     * @throws Main\ObjectException
     */
    private function isSumCorrect(Payment $payment, $sum): bool
    {
        PaySystem\Logger::addDebugInfo(
            __CLASS__ . ': payselectionSum=' . PriceMaths::roundPrecision($sum) . "; paymentSum=" . PriceMaths::roundPrecision($payment->getSum())
        );

        return PriceMaths::roundPrecision($sum) === PriceMaths::roundPrecision($payment->getSum());
    }

    /**
     * @param Request $request
     * @param int $paySystemId
     * @return bool
     */
    public static function isMyResponse(Request $request, $paySystemId): bool
    {
        $inputStream = static::readFromStream();
        if ($inputStream) {
            $data = static::decode($inputStream);
            if ($data === false) {
                return false;
            }
            if (isset($data['OrderId'])) {
                [, $trackingPaySystemId] = explode(self::TRACKING_ID_DELIMITER, $data['OrderId']);
                return (int)$trackingPaySystemId === (int)$paySystemId;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     * @return bool|int|mixed
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        $inputStream = static::readFromStream();
        if ($inputStream) {
            $data = static::decode($inputStream);
            if (isset($data['OrderId'])) {
                [$trackingPaymentId] = explode(self::TRACKING_ID_DELIMITER, $data['OrderId']);
                return (int)$trackingPaymentId;
            }
        }

        return false;
    }

    /**
     * @param Payment $payment
     * @return string
     * @throws Main\ArgumentException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    private function getPaymentDescription(Payment $payment): string
    {
        /** @var PaymentCollection $collection */
        $collection = $payment->getCollection();
        $order = $collection->getOrder();
        $userEmail = $order->getPropertyCollection()->getUserEmail();

        return str_replace(
            [
                '#PAYMENT_NUMBER#',
                '#ORDER_NUMBER#',
                '#PAYMENT_ID#',
                '#ORDER_ID#',
                '#USER_EMAIL#'
            ],
            [
                $payment->getField('ACCOUNT_NUMBER'),
                $order->getField('ACCOUNT_NUMBER'),
                $payment->getId(),
                $order->getId(),
                ($userEmail) ? $userEmail->getValue() : ''
            ],
            $this->getBusinessValue($payment, 'PAYSELECTION_PAYMENT_DESCRIPTION')
        );
    }

    /**
     * @param Payment $payment
     * @return string
     */
    private function getSuccessUrl(Payment $payment): string
    {
        return $this->getBusinessValue($payment, 'PAYSELECTION_SUCCESS_URL') ?: $this->service->getContext()->getUrl();
    }

    /**
     * @param Payment $payment
     * @return string
     */
    private function getDeclineUrl(Payment $payment): string
    {
        return $this->getBusinessValue($payment, 'PAYSELECTION_DECLINE_URL') ?: $this->service->getContext()->getUrl();
    }

    /**
     * @param Payment $payment
     * @return string
     */
    private function getFailUrl(Payment $payment): string
    {
        return $this->getBusinessValue($payment, 'PAYSELECTION_FAIL_URL') ?: $this->service->getContext()->getUrl();
    }

    /**
     * @param Payment $payment
     * @return string
     */
    private function getCancelUrl(Payment $payment): string
    {
        return $this->getBusinessValue($payment, 'PAYSELECTION_CANCEL_URL') ?: $this->service->getContext()->getUrl();
    }

    /**
     * @param Payment $payment
     * @return string
     */
    private function getNotificationUrl(Payment $payment): string
    {
        return $this->getBusinessValue($payment, 'PAYSELECTION_NOTIFICATION_URL');
    }

    /**
     * @param Payment $payment
     * @param string $body
     * @return array
     */
    private function getHeaders(Payment $payment, string $body='', string $url='', string $method='POST'): array
    {
        $secretKey = $this->getBusinessValue($payment, 'PAYSELECTION_SECRET_KEY');
        $uid = $this->getIdempotenceKey();
        $site_id = $this->getBusinessValue($payment, 'PAYSELECTION_SITE_ID');
        $path = parse_url($url, PHP_URL_PATH);
        $msg = $method.PHP_EOL.
            $path.PHP_EOL.
            $site_id.PHP_EOL.
            $uid.PHP_EOL.
            $body;
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-SITE-ID' => $site_id,
            'X-REQUEST-ID' => $uid,
            'X-REQUEST-SIGNATURE' => $this->getSignature($msg, $secretKey),
        ];
    }

    /**
     *
     */
    private function getSignature(string $body, string $secretKey): string
    {
        return hash_hmac('sha256', $body, $secretKey, false);
    }

    /**
     * @return bool
     */
    private function isWidgetMode(): bool
    {
        return $this->service->getField('PS_MODE') === self::MODE_WIDGET;
    }

    /**
     * @return bool
     */
    private function isCheckoutMode(): bool
    {
        return $this->service->getField('PS_MODE') === self::MODE_CHECKOUT;
    }

    /**
     * @return string
     */
    private function getIdempotenceKey(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * @param Payment $payment
     * @param string $action
     * @return string
     */
    protected function getUrl(Payment $payment = null, $action): string
    {
        $url = '';
        if ($payment !== null && $action === 'getPaymentCreate') {
            $host = $this->getBusinessValue($payment, 'PAYSELECTION_CHECKOUT_API_URL');
            $url = $host . '/webpayments/create';
        } else if ($payment !== null && $action === 'getPaymentStatus') {
            $host = $this->getBusinessValue($payment, 'PAYSELECTION_GATEWAY_API_URL');
            $url = $host . '/transactions/#transaction_id#';
            $url = str_replace('#transaction_id#', $payment->getField('PS_INVOICE_ID'), $url);
        } else if ($payment !== null && $action === 'refund') {
            $host = $this->getBusinessValue($payment, 'PAYSELECTION_GATEWAY_API_URL');
            $url = $host . '/payments/refund';
        } else if ($payment !== null && $action === 'cancel') {
            $host = $this->getBusinessValue($payment, 'PAYSELECTION_GATEWAY_API_URL');
            $url = $host . '/payments/cancellation';
        }
        return $url;
    }

    /**
     * @return array
     */
    protected function getUrlList(): array
    {
        return [
        ];
    }

    /**
     * @return bool|string
     */
    private static function readFromStream()
    {
        return file_get_contents('php://input');
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Main\ArgumentException
     */
    private static function encode(array $data)
    {
        return Main\Web\Json::encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $data
     * @return mixed
     */
    private static function decode(string $data)
    {
        try {
            return Main\Web\Json::decode($data);
        } catch (Main\ArgumentException $exception) {
            return false;
        }
    }

    private function roundNumber($num): string
    {
        return number_format($num, 2, '.', '');
    }
}