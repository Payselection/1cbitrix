<?
$MESS["SALE_PAYSELECTION"] = "Payselection";
$MESS["SALE_PAYSELECTION_CANCEL_URL"] = "Redirect customer to this URL when the transaction is canceled";
$MESS["SALE_PAYSELECTION_CANCEL_URL_DESC"] = "Leave empty to auto redirect the customer to a page they were seeing before setup";
$MESS["SALE_PAYSELECTION_CHANGE_STATUS_PAY"] = "Auto change order status to paid when setup success status is received";
$MESS["SALE_PAYSELECTION_GATEWAY_API_URL"] = "URL, API Payment Gateway";
$MESS["SALE_PAYSELECTION_CHECKOUT_API_URL"] = "URL, WebForm";
$MESS["SALE_PAYSELECTION_WIDGET_API_URL"] = "URL, Widget";
$MESS["SALE_PAYSELECTION_CONNECT_SETTINGS"] = "Payselection Connection Settings";
$MESS["SALE_PAYSELECTION_DECLINE_URL"] = "Redirect customer to this URL when a bank declines the transaction";
$MESS["SALE_PAYSELECTION_DECLINE_URL_DESC"] = "Leave empty to auto redirect the customer to a page they were seeing before setup";
$MESS["SALE_PAYSELECTION_FAIL_URL"] = "Redirect customer to this URL upon failed transaction";
$MESS["SALE_PAYSELECTION_FAIL_URL_DESC"] = "Leave empty to auto redirect the customer to a page they were seeing before setup";
$MESS["SALE_PAYSELECTION_ID"] = "ID(SITE-ID)";
$MESS["SALE_PAYSELECTION_NOTIFICATION_URL"] = "Notification URL";
$MESS["SALE_PAYSELECTION_PAYMENT_DESCRIPTION"] = "Order description";
$MESS["SALE_PAYSELECTION_PAYMENT_DESCRIPTION_DESC"] = "Text may include: #PAYMENT_ID# - setup ID, #ORDER_ID# - order ID, #PAYMENT_NUMBER# - setup ref., #ORDER_NUMBER# - order ref., #USER_EMAIL# - customer email";
$MESS["SALE_PAYSELECTION_PAYMENT_DESCRIPTION_TEMPLATE"] = "Payment ##PAYMENT_NUMBER# for order ##ORDER_NUMBER# for #USER_EMAIL#";
$MESS["SALE_PAYSELECTION_SECRET_KEY"] = "Secret key";
$MESS["SALE_PAYSELECTION_KEY"] = "Public key";
$MESS["SALE_PAYSELECTION_SUCCESS_URL"] = "Redirect customer to this URL upon successful transaction";

$MESS["SALE_PAYSELECTION_TYPE_SYSTEM"] = "Payment scheme type";
$MESS["SALE_PAYSELECTION_TYPE_SCHEME_0"] = "One-step payment";
$MESS["SALE_PAYSELECTION_TYPE_SCHEME_1"] = "Two-stage payment";

$MESS["SALE_PAYSELECTION_GROUP_RECEIPT"] = "Fiscalization";
$MESS["SALE_PAYSELECTION_RECEIPT_NAME"] = "Send cart data(including customer info)";
$MESS["SALE_PAYSELECTION_RECEIPT_DESCR"] = "If this option is enabled order receipts will be created and sent to your customer and to the revenue service via Payselection.";
$MESS["SALE_PAYSELECTION_PAYMENT_INN"] = "INN organization";
$MESS["SALE_PAYSELECTION_PAYMENT_ADDRESS"] = "Legal address";
$MESS["SALE_PAYSELECTION_PAYMENT_EMAIL"] = "Email organization";

$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_DELIVERY_METHOD_NAME"] = 'Payment type from delivery';
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_NAME"] = 'Payment type';
$MESS['SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_1'] = "Full advance payment before the transfer of the subject of calculation";
$MESS['SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_2'] = "Partial prepayment until the transfer of the subject of calculation";
$MESS['SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_3'] = "Prepaid expense";
$MESS['SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_4'] = "Full payment at the time of transfer of the subject of calculation";
$MESS['SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_5'] = "Partial payment of the subject of payment at the time of its transfer with subsequent payment on credit";
$MESS['SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_6'] = "Transfer of the subject of calculation without its payment at the time of its transfer with subsequent payment on credit";
$MESS['SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_7'] = "Payment of the subject of calculation after its transfer with payment on credit";

$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_NAME"] = "Type of goods and services";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_DELIVERY_NAME"] = 'Type of goods and services from delivery';
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_1"] = "Goods";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_2"] = "Excised goods";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_3"] = "Job";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_4"] = "Service";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_5"] = "Gambling bet";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_6"] = "Win in gambling";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_7"] = "Lottery ticket";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_8"] = "Lottery win";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_9"] = "Intellectual property provision";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_10"] = "Payment";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_11"] = "Agent's commission";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_12"] = "Combined";
$MESS["SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_13"] = "Other";

$MESS["SALE_PAYSELECTION_RECEIPT_TAX_NAME"] = "Taxation system";
$MESS["SALE_PAYSELECTION_RECEIPT_VALUE_0"] = "General";
$MESS["SALE_PAYSELECTION_RECEIPT_VALUE_1"] = "Simplified, income";
$MESS["SALE_PAYSELECTION_RECEIPT_VALUE_2"] = "Simplified, income minus expences";
$MESS["SALE_PAYSELECTION_RECEIPT_VALUE_3"] = "Unified tax on imputed income";
$MESS["SALE_PAYSELECTION_RECEIPT_VALUE_4"] = "Unified agricultural tax";
$MESS["SALE_PAYSELECTION_RECEIPT_VALUE_5"] = "Patent taxation system";

$MESS["SALE_PAYSELECTION_NDS_NAME"] = "Item-dependent tax (VAT)";
$MESS["SALE_PAYSELECTION_NDS_DESCR"] = "Be sure to specify if you use receipt printing through Payselection";
$MESS["SALE_PAYSELECTION_NDS_none"] = "tax excluded";
$MESS["SALE_PAYSELECTION_NDS_vat0"] = "VAT at 0%";
$MESS["SALE_PAYSELECTION_NDS_vat10"] = "VAT receipt at rate 10%";
$MESS["SALE_PAYSELECTION_NDS_vat18"] = "VAT receipt at rate 18%";
$MESS["SALE_PAYSELECTION_NDS_vat110"] = "VAT check at the estimated rate 10/110";
$MESS["SALE_PAYSELECTION_NDS_vat118"]  = "VAT check at the estimated rate 18/118";
