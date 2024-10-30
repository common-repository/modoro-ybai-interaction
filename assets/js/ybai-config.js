/**
 * Created by thanhphuc on 8/18/17.
 */
jQuery(function($){
    $("#check_connection_ybai").submit(function(event){
        event.preventDefault();

        var dataconnection = $("#check_connection_ybai").serializeObject();

        jQuery.ajax({
            url: ybai_ajax_object.admin_url,
            dataType: 'json',
            method: "POST",
            data:
                {
                    action: 'ybai_connect',
                    data: dataconnection
                },
            success: function(result) {
                alert(result.message);
            }
        });

        jQuery.ajax({
            url: ybai_ajax_object.option_url,
            method: "POST",
            data: dataconnection
        });
    });

    $("#ybai_sync_all").click(function(event){
        event.preventDefault();
        $("#ybai_sync_all").hide();
        $("#ybai_sync_all_loading").show();
        jQuery.ajax({
            url: ybai_ajax_object.admin_url,
            dataType: 'json',
            method: "POST",
            data:
                {
                    action: 'ybai_synchronize_all',
                    data: {}
                },
            success: function(result){
                $("#ybai_sync_all").show();
                $("#ybai_sync_all_loading").hide();
                if(result){
                    alert(result.message);
                }
                else
                    alert('Kết nối máy chủ thất bại, Vui lòng thử lại!');
            }
        });
    });
});
