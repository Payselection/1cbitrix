<? CJSCore::Init( 'jquery' ); ?>
<script>
    function payselection_pay_link(id) {
        $.ajax({
            type: 'GET',
            url: '/payselection_create_link/payselection_pay_link.php?Order_ID='+id,
            success: function(data) {
                alert(data);
            },
            error: function(xhr, str){
                alert('Возникла ошибка: ' + xhr.responseCode);
            }
        });
    }
</script>