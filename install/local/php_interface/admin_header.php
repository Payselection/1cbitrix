<? CJSCore::Init('jquery');
$language = LANGUAGE_ID; ?>
<script>
    function payselection_pay_link(id) {

        var messages = {
            "en": {
                "PAYMENT_SUCCESS_MESSAGE": "Email sent successfully!",
                "ERROR_MESSAGE": "An error occurred"
            },
            "ru": {
                "PAYMENT_SUCCESS_MESSAGE": "Письмо отправлено успешно!",
                "ERROR_MESSAGE": "Возникла ошибка"
            }
        };
        $.ajax({
            type: 'GET',
            url: '/payselection_create_link/payselection_pay_link.php?Order_ID=' + id,
            success: function (data) {
                alert(messages['<?php echo $language; ?>'][ "PAYMENT_SUCCESS_MESSAGE"]);
            },
            error: function (xhr, str) {
                alert(messages['<?php echo $language; ?>'][ "ERROR_MESSAGE"]  + xhr.responseCode);
            }
        });
    }
</script>