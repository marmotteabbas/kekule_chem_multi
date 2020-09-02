/* eslint no-undef: "off"*/
define(['jquery'], function($) {
    return {
            init: function() {
            //test for iterating over child elements
            var langArray = [];
            $('.vodiapicker > span > select#id_arrows_transfo_0 option').each(function () {
              var img = $(this).attr("value");
              img = "type/kekule_chem_multi/img/"+img+".png";
              var text = this.innerText;
              var value = $(this).val();
              var item = '<li><img src="' + img + '" alt="" value="' + value + '"/><span>' + text + '</span></li>';
              langArray.push(item);
            });
            $('#a').html(langArray);

            //Set the button value to the first el of the array
            $('.btn-select').html(langArray[0]);
            $('.btn-select').attr('value', 'simple_arrow');

            //change button stuff on click
            $('#a li').click(function () {
              var indice = $(this).parent().parent().parent().parent().parent().attr("data-groupname").replace("answeroptions[", "").substring(0,1);
              var img = $(this).find('img').attr("src");
              var value = $(this).find('img').attr('value');
              var text = this.innerText;
              var item = '<li><img src="' + img + '" alt="" /><span>' + text + '</span></li>';
              $($(this).parent().parent().parent().children().first()).html(item);
              $('.vodiapicker > span > select#id_arrows_transfo_'+indice+' option[value='+value+']').attr('selected','selected');

              if (value == "simple_arrow") { 
                    $('.vodiapicker > span > select#id_arrows_transfo_'+indice+' option[value=double_arrow]').removeAttr('selected');
              } else {
                    $('.vodiapicker > span > select#id_arrows_transfo_'+indice+' option[value=simple_arrow]').removeAttr('selected'); 
              }
              $(this).parent().parent().parent().children().eq(1).toggle();

            });
             $(this).parent().parent().parent().children().eq(1).toggle();
            $(".btn-select").click(function () {
              $(this).parent().children().eq(1).toggle();
            });

            //check local storage for the lang
            var sessionLang = localStorage.getItem('lang');
            if (sessionLang) {
              //find an item with value of sessionLang
              var langIndex = langArray.indexOf(sessionLang);
              $('.btn-select').html(langArray[langIndex]);
              $('.btn-select').attr('value', sessionLang);
            } else {
              var langIndex = langArray.indexOf('ch');
              console.log(langIndex);
              $('.btn-select').html(langArray[langIndex]);
            }
            
            $('.vodiapicker > span > select').each(function () {
                if ("double_arrow" == $(this).val()) {
                    $(this).parent().parent().parent().find(".arrow_selector").children().first().html(langArray[1]);
                    $(this).parent().parent().parent().find(".arrow_selector").attr('value', 'double_arrow');
                }  
            });
            }
        };
});