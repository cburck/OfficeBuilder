/**
 * Created by cburck on 8/26/15.
 */

indexController = {

    run : function() {
        var self = this;

        //change event for favorite color drop-down
        $("#fav-color").bind("change", function() {

            $("#color-update").hide();

            var params = {"favorite_color":$(this).val(),"id":$(this).attr('data')};

            $.post("/users/set-favorite-color/format/json", params, function(returned){
                if (returned.response.error === true) {
                    alert(returned.response.errorMsg);
                } else {
                    $("#color-update").show();
                }
            }, "json");
        });
    }
};

$(function() {
    indexController.run();
});