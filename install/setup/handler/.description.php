<?php
use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Sale\PaySystem;

Loc::loadMessages(__FILE__);

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$protocol = $request->isHttps() ? 'https://' : 'http://';

$isAvailable = PaySystem\Manager::HANDLER_AVAILABLE_TRUE;

$portalZone = Loader::includeModule('intranet') ? CIntranetUtils::getPortalZone() : '';
$licensePrefix = Loader::includeModule('bitrix24') ? \CBitrix24::getLicensePrefix() : '';

$data = [
	'NAME' => Loc::getMessage('SALE_PAYSELECTION'),
	'SORT' => 100,
	'CODES' => [
		'PAYSELECTION_SITE_ID' => [
			'NAME' => 'ID',
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_ID_DESC'),
			'SORT' => 100,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
		],
		'PAYSELECTION_SECRET_KEY' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_SECRET_KEY'),
			'SORT' => 200,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
		],
        'PAYSELECTION_CHECKOUT_API_URL' => [
            'NAME' => Loc::getMessage('SALE_PAYSELECTION_CHECKOUT_API_URL'),
            'SORT' => 300,
            'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
            'DEFAULT' => [
                'PROVIDER_KEY' => 'VALUE',
                'PROVIDER_VALUE' => 'https://gw.payselection.com',
            ],
        ],
		'PAYSELECTION_PAYMENT_DESCRIPTION' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_DESCRIPTION'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_DESCRIPTION_DESC'),
			'SORT' => 400,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
			'DEFAULT' => [
				'PROVIDER_KEY' => 'VALUE',
				'PROVIDER_VALUE' => Loc::getMessage('SALE_PAYSELECTION_PAYMENT_DESCRIPTION_TEMPLATE'),
			],
		],
		'PAYSELECTION_NOTIFICATION_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_NOTIFICATION_URL'),
			'SORT' => 500,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
			'DEFAULT' => [
				'PROVIDER_KEY' => 'VALUE',
				'PROVIDER_VALUE' => $protocol.$request->getHttpHost().'/bitrix/tools/sale_ps_result.php',
			],
		],
		'PAYSELECTION_SUCCESS_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_SUCCESS_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_SUCCESS_URL_DESC'),
			'SORT' => 600,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
		],
		'PAYSELECTION_DECLINE_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_DECLINE_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_DECLINE_URL_DESC'),
			'SORT' => 700,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
		],
		'PAYSELECTION_FAIL_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_FAIL_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_FAIL_URL_DESC'),
			'SORT' => 800,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
		],
		'PAYSELECTION_CANCEL_URL' => [
			'NAME' => Loc::getMessage('SALE_PAYSELECTION_CANCEL_URL'),
			'DESCRIPTION' => Loc::getMessage('SALE_PAYSELECTION_CANCEL_URL_DESC'),
			'SORT' => 900,
			'GROUP' => 'SALE_PAYSELECTION_CONNECT_SETTINGS',
		],
	]
];
