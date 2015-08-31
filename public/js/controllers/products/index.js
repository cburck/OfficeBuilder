/**
 * Created by cburck on 8/29/15.
 */

indexController = {

    run : function() {
        var self = this;

        //change event for inventory units
        $(".inventory-units").change(function() {
            var textbox = $(this);
            var params = {"inventory_id": textbox.data('inventory-id'),
                          "units": parseInt(textbox.val())};

            $.post("/products/set-inventory-units/format/json", params, function(returned){
                if (returned.response.error === true) {
                    alert(returned.response.errorMsg);
                } else {
                    location.reload();
                }
            }, "json");
        });

        //purchase button
        $(".purchase-inventory").click(function() {
            var button = $(this);
            var price = button.parent().siblings('.price').html();

            if (confirm('Are you sure you want to purchase this? Your credit card on file will be charged ' + price +'.')) {
                var params = {"inventory_id": button.data('inventory-id')};

                $.post("/products/purchase/format/json", params, function(returned){
                    if (returned.response.error === true) {
                        alert(returned.response.errorMsg);
                    } else {
                        //update available units and hide purchase button
                        button.parent().siblings('.availble-units').html(returned.response.inventoryUnits);
                        button.remove();
                    }
                }, "json");
            }
        });
    }
};

$(function() {
    indexController.run();
});
