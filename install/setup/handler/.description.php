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
        'PAYSELECTION_RECEIPT' => array(
            'NAME' => Loc::getMessage("SALE_PAYSELECTION_RECEIPT_NAME"),
            'DESCRIPTION' => Loc::getMessage("SALE_PAYSELECTION_RECEIPT_DESCR"),
            'SORT' => 400,
            'GROUP' => Loc::getMessage("SALE_PAYSELECTION_GROUP_RECEIPT"),
            'INPUT' => array(
                'TYPE' => 'Y/N'
            ),
            'DEFAULT' => array(
                'PROVIDER_VALUE' => "N",
                'PROVIDER_KEY' => "INPUT"
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
        "PAYSELECTION_PAYMENT_TAX" => array(
            "NAME" => Loc::getMessage("SALE_PAYSELECTION_RECEIPT_TAX_NAME"),
            'SORT' => 440,
            'GROUP' => Loc::getMessage("SALE_PAYSELECTION_GROUP_RECEIPT"),
            'TYPE' => 'SELECT',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    "osn"  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_0'),
                    "usn_income"  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_1'),
                    "usn_income_outcome"  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_2'),
                    "envd"  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_3'),
                    "esn"  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_4'),
                    "patent"  =>  GetMessage('SALE_PAYSELECTION_RECEIPT_VALUE_5'),
                )
            ),
            'DEFAULT' => array(
                "PROVIDER_VALUE" => "osn",
                "PROVIDER_KEY" => "INPUT"
            )
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
