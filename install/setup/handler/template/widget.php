<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$sum = round($params['sum'], 2);
?>

<div class="mb-4" >
	<p><?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_TEMPLATE_PAYSELECTION_WIDGET_DESCRIPTION') ?></p>
	<p><?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_TEMPLATE_PAYSELECTION_WIDGET_SUM',
			[
				'#SUM#' => SaleFormatCurrency($sum, $params['currency']),
			]
		) ?></p>
	<div class="d-flex align-items-center mb-3" id="paysystem-button">
		<div class="col-auto pl-0">
			<a class="btn btn-lg btn-success pl-4 pr-4" style="border-radius: 32px;" id="paysystem-button-pay" href="#"><?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_TEMPLATE_PAYSELECTION_WIDGET_BUTTON_PAID') ?></a>
		</div>
	</div>

    <div class="alert alert-info"><?= Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_TEMPLATE_PAYSELECTION_WIDGET_WARNING_RETURN') ?></div>
</div>

<script src="<?= CUtil::JSEscape($params['WidgetUrl']) ?>"></script>
<script type="text/javascript">
    this.pay = function() {
        let widget = new pw.PayWidget();
        let pay =
            {
                MetaData: {
                    PaymentType: "<?= CUtil::JSEscape($params['payment_type']) ?>",
                    PreviewForm: <?= CUtil::JSEscape($params['preview_form']) ?>,
                },
                PaymentRequest: {
                    OrderId: "<?= CUtil::JSEscape($params['PaymentRequest']['OrderId']) ?>",
                    Amount: "<?= CUtil::JSEscape($params['PaymentRequest']['Amount']) ?>",
                    Currency: "<?= CUtil::JSEscape($params['PaymentRequest']['Currency']) ?>",
                    Description: "<?= CUtil::JSEscape($params['PaymentRequest']['Description']) ?>",
                    ExtraData: JSON.parse("<?= CUtil::JSEscape(json_encode($params['PaymentRequest']['ExtraData'])) ?>"),
                },
            };
        if ("<?= CUtil::JSEscape(json_encode($params['ReceiptData'])) ?>" !== "null") {
            pay['ReceiptData'] = JSON.parse("<?= CUtil::JSEscape(json_encode($params['ReceiptData'])) ?>");
        }
        widget.pay(
            {
                serviceId: "<?= CUtil::JSEscape($params['ServiceId']) ?>",
                key: "<?= CUtil::JSEscape($params['Key']) ?>",
            },
            pay,
            {
                onSuccess: function (res) {
                    console.log("onSuccess from shop", res);
                    if (!isEmpty(res.returnUrl)) {
                        window.location.href = res.returnUrl;
                    }
                },
                onError: function (res) {
                    console.log("onFail from shop", res);
                    if (!isEmpty(res.returnUrl)) {
                        window.location.href = res.returnUrl;
                    } else {
                        window.location.reload();
                    }
                },
                onClose: function (res) {
                    console.log("onClose from shop", res);
                    if (!isEmpty(res.returnUrl)) {
                        window.location.href = res.returnUrl;
                    } else {
                        window.location.reload();
                    }
                },
            },
        );
    };

    let a = document.getElementById('paysystem-button-pay');
    if (a) {
        a.onclick = function (e) {
            e.preventDefault();
            pay();
            document.getElementById("paysystem-button").remove();
        }
    }
    function isEmpty(str) {
        return (!str || 0 === str.length);
    }
</script>
