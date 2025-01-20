<?php
use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Sale\PaySystem;

Loc::loadMessages(__FILE__);

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$protocol = $request->isHttps() ? 'https://' : 'http://';

$data = [
	'NAME' => Loc::getMessage('SALE_PAYSELECTION'),
	'SORT' => 100,
	'CODES' => [
		'PAYSELECTION_SITE_ID' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_ID'),
			'SORT' => 100,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
		],
		'PAYSELECTION_SECRET_KEY' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_SECRET_KEY'),
			'SORT' => 110,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
		],
        'PAYSELECTION_KEY' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_KEY'),
            'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_KEY'),
            'SORT' => 120,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
        ],
        'PAYSELECTION_CHECKOUT_API_URL' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_CHECKOUT_API_URL'),
            'SORT' => 200,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
            'DEFAULT' => [
                'PROVIDER_KEY' => 'VALUE',
                'PROVIDER_VALUE' => 'https://webform.payselection.com',
            ],
        ],
        'PAYSELECTION_GATEWAY_API_URL' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_GATEWAY_API_URL'),
            'SORT' => 210,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
            'DEFAULT' => [
                'PROVIDER_KEY' => 'VALUE',
                'PROVIDER_VALUE' => 'https://gw.payselection.com',
            ],
        ],
        'PAYSELECTION_WIDGET_API_URL' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_WIDGET_API_URL'),
            'SORT' => 220,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
            'DEFAULT' => [
                'PROVIDER_KEY' => 'VALUE',
                'PROVIDER_VALUE' => 'https://widget.payselection.com/lib/pay-widget.js',
            ],
        ],
		'PAYSELECTION_PAYMENT_DESCRIPTION' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_DESCRIPTION'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_DESCRIPTION_DESC'),
			'SORT' => 300,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
			'DEFAULT' => [
				'PROVIDER_KEY' => 'VALUE',
				'PROVIDER_VALUE' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_DESCRIPTION_TEMPLATE'),
			],
		],
        'PAYSELECTION_PAYMENT_TYPE_SYSTEM' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_TYPE_SYSTEM'),
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    '0'  => Loc::getMessage('SALE_PAYSELECTION_TYPE_SCHEME_0'),
                    '1'  => Loc::getMessage('SALE_PAYSELECTION_TYPE_SCHEME_1'),
                )
            ),
            'SORT' => 310,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => '0',
                'PROVIDER_KEY' => 'INPUT'
            )
        ),
        'PS_CHANGE_STATUS_PAY' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_CHANGE_STATUS_PAY'),
            'SORT' => 320,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
            'INPUT' => [
                'TYPE' => 'Y/N',
            ],
            'DEFAULT' => [
                'PROVIDER_KEY' => 'INPUT',
                'PROVIDER_VALUE' => 'Y',
            ],
        ],
        'PAYSELECTION_RECEIPT' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_RECEIPT_NAME'),
            'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_RECEIPT_DESCR'),
            'SORT' => 400,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
            'INPUT' => array(
                'TYPE' => 'Y/N'
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'N',
                'PROVIDER_KEY' => 'INPUT'
            )
        ),
        'PAYSELECTION_PAYMENT_INN' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_INN'),
            'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_INN'),
            'SORT' => 410,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
        ],
        'PAYSELECTION_PAYMENT_EMAIL' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_EMAIL'),
            'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_EMAIL'),
            'SORT' => 420,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
        ],
        'PAYSELECTION_PAYMENT_ADDRESS' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_ADDRESS'),
            'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_ADDRESS'),
            'SORT' => 430,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
        ],
        'PAYSELECTION_PAYMENT_METHOD' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_NAME'),
            'SORT' => 431,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
            'TYPE' => 'SELECT',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'full_prepayment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_1'),
                    'prepayment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_2'),
                    'advance' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_3'),
                    'full_payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_4'),
                    'partial_payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_5'),
                    'credit' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_6'),
                    'credit_payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_7'),
                )
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'full_prepayment',
                'PROVIDER_KEY' => 'INPUT'
            )
        ),
        'PAYSELECTION_PAYMENT_OBJECT' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_NAME'),
            'SORT' => 432,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
            'TYPE' => 'SELECT',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'commodity' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_1'),
                    'excise' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_2'),
                    'job' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_3'),
                    'service' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_4'),
                    'gambling_bet' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_5'),
                    'gambling_prize' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_6'),
                    'lottery' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_7'),
                    'lottery_prize' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_8'),
                    'intellectual_activity' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_9'),
                    'payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_10'),
                    'agent_commission' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_11'),
                    'composite' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_12'),
                    'award' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_13'),
                )
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'commodity',
                'PROVIDER_KEY' => 'INPUT'
            )
        ),
        'PAYSELECTION_PAYMENT_OBJECT_DELIVERY' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_DELIVERY_NAME'),
            'SORT' => 433,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
            'TYPE' => 'SELECT',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'commodity' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_1'),
                    'excise' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_2'),
                    'job' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_3'),
                    'service' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_4'),
                    'gambling_bet' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_5'),
                    'gambling_prize' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_6'),
                    'lottery' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_7'),
                    'lottery_prize' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_8'),
                    'intellectual_activity' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_9'),
                    'payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_10'),
                    'agent_commission' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_11'),
                    'composite' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_12'),
                    'award' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_OBJECT_VALUE_13'),
                )
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'service',
                'PROVIDER_KEY' => 'INPUT'
            )
        ),
        'PAYSELECTION_PAYMENT_METHOD_DELIVERY' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_DELIVERY_METHOD_NAME'),
            'SORT' => 434,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
            'TYPE' => 'SELECT',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'full_prepayment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_1'),
                    'prepayment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_2'),
                    'advance' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_3'),
                    'full_payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_4'),
                    'partial_payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_5'),
                    'credit' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_6'),
                    'credit_payment' => GetMessage('SALE_PAYSELECTION_RECEIPT_PAYMENT_METHOD_VALUE_7'),
                )
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'full_prepayment',
                'PROVIDER_KEY' => 'INPUT'
            )
        ),
        'PAYSELECTION_PAYMENT_TAX' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_RECEIPT_TAX_NAME'),
            'SORT' => 440,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
            'TYPE' => 'SELECT',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'osn'  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_0'),
                    'usn_income'  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_1'),
                    'usn_income_outcome'  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_2'),
                    'envd'  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_3'),
                    'esn'  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_4'),
                    'patent'  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_5'),
                )
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'osn',
                'PROVIDER_KEY' => 'INPUT'
            )
        ),
        'PAYSELECTION_PAYMENT_NDS' => array(
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_NDS_NAME'),
            'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_NDS_DESCR'),
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'none'  => Loc::getMessage('SALE_PAYSELECTION_NDS_none'),
                    'vat0'  => Loc::getMessage('SALE_PAYSELECTION_NDS_vat0'),
                    'vat10' => Loc::getMessage('SALE_PAYSELECTION_NDS_vat10'),
                    'vat20' => Loc::getMessage('SALE_PAYSELECTION_NDS_vat20'),
                    'vat110'=> Loc::getMessage('SALE_PAYSELECTION_NDS_vat110'),
                    'vat120'=> Loc::getMessage('SALE_PAYSELECTION_NDS_vat120')
                )
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => 'none',
                'PROVIDER_KEY' => 'INPUT'
            ),
            'SORT' => 450,
            'GROUP' => Loc::getMessage('SALE_PAYSELECTION_GROUP_RECEIPT'),
        ),
		'PAYSELECTION_NOTIFICATION_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_NOTIFICATION_URL'),
			'SORT' => 500,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
			'DEFAULT' => [
				'PROVIDER_KEY' => 'VALUE',
				'PROVIDER_VALUE' => $protocol.$request->getHttpHost().'/bitrix/tools/sale_ps_result.php',
			],
		],
		'PAYSELECTION_SUCCESS_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_SUCCESS_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_SUCCESS_URL_DESC'),
			'SORT' => 510,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
		],
		'PAYSELECTION_DECLINE_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_DECLINE_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_DECLINE_URL_DESC'),
			'SORT' => 520,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
		],
		'PAYSELECTION_FAIL_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_FAIL_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_FAIL_URL_DESC'),
			'SORT' => 530,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
		],
		'PAYSELECTION_CANCEL_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_CANCEL_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_CANCEL_URL_DESC'),
			'SORT' => 540,
			'GROUP' => Loc::getMessage('SALE_PAYSELECTION_CONNECT_SETTINGS'),
		],
	]
];
