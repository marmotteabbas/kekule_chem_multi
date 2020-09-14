/* eslint no-undef: "off"*/
define(['jquery'], function($) {
    return {
        init: function() {
            $(".custom-select").change(function() { 
                var name = $(this).attr('id').substr(0,$(this).attr('id').length-1);
                var num = $(this).attr('id').substr($(this).attr('id').length-1,$(this).attr('id').length-1);
                if (name == "id_draw_grade_") {
                    $("#id_arrows_grade_"+num).val(1-$(this).val());
                }

                if (name == "id_arrows_grade_") {
                    $("#id_draw_grade_"+num).val(1-$(this).val());
                }
            });  
        }
        
    }
});