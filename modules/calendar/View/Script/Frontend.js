var calendarFrontend = {
    
    _validation : function(form) {
        $form = form;
        that  = this;
        var frmError = false;
        
        $form.find('[class*=validate]').each(function(){
            $field = $J(this);
            if ( !that._validateField($field) ) {
                $field.css('border', '#ff0000 1px solid');
                $J("#calendarErrorMessage").show();
                frmError = true;
            } else {
                $field.css('border', '');
            }
        });
        
        if ( frmError ) {
            return false;
        }
        
        return true;
    },
    _validateField : function(field) {
        /**
         * inspired from validation Engine
         */
        var rules = /validate\[(.*)\]/.exec($field.attr('class'));

        if (!rules)
            return false;

        var str = rules[1];
        var rules = str.split(/\[|,|\]/);
        // Fix for adding spaces in the rules
        for (var i = 0; i < rules.length; i++) {
            rules[i] = rules[i].replace(" ", "");
            // Remove any parsing errors
            if (rules[i] === '') {
                    delete rules[i];
            }
        }
        
        for (var i = 0; i < rules.length; i++) {            
            switch (rules[i]) {
                case "event_title":
                    language = field.attr("data-id");        
                    if ($J("#showIn_"+language).is(":checked")) {
                        var field_val      = $J.trim( field.val() );
                        if (
                                   ( !field_val )                            
                        ) {
                                return false;
                        }
                        return true;
                    }
                    return true;
                    break;
                case "required":
                    switch (field.prop("type")) {
                        case "text":
                        case "password":
                        case "textarea":
                        case "file":
                        case "select-one":
                        case "select-multiple":
                        default:
                                var field_val      = $J.trim( field.val() );                    
                                if (
                                           ( !field_val )                            
                                ) {
                                        return false;
                                }
                                return true;
                                break;
                        case "radio":
                        case "checkbox":
                                var form = field.closest("form");
                                var name = field.attr("name");
                                if (form.find("input[name='" + name + "']:checked").size() == 0) {
                                        if (form.find("input[name='" + name + "']:visible").size() == 1)
                                                return true;
                                        else
                                                return false;
                                }
                                break;                            
                    }
                    break;
            }
        }
        

    }
};
var modifyEvent = {
    // elm => jquery object
    _handleSeriesEventRowDisplay : function(elm){
      if (elm.is(":checked")) {
          $J('.series-event-row').show();
          showOrHide();
      } else {
          $J('.series-event-row').hide();
      }
    },
    _handleAllDayEvent : function(elm){        
      jQuery(".startDate").data('dateTime', jQuery(".startDate").datetimepicker("getDate").getTime());
      jQuery(".endDate").data('dateTime', jQuery(".endDate").datetimepicker("getDate").getTime());
      if (elm.is(":checked")) {
         // new initialization instead of show up once
         jQuery(".startDate").datepicker('setDate', new Date(jQuery(".startDate").data('dateTime')));
         jQuery(".endDate").datepicker('setDate', new Date(jQuery(".endDate").data('dateTime')));
         jQuery( ".startDate, .endDate" ).datetimepicker('disableTimepicker');
      } else {
         jQuery(".startDate, .endDate").datetimepicker('enableTimepicker');
      }
      jQuery(".startDate").datepicker('setDate', new Date(jQuery(".startDate").data('dateTime')));
      jQuery(".endDate").datepicker('setDate', new Date(jQuery(".endDate").data('dateTime')));
    },
    _isNumber : function(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
          return false;
      }
      return true;
    }
};

$J(function(){
    $J(".all_day").click(function(){
        modifyEvent._handleAllDayEvent($J(this));
    });
    var $eventTab = $J("#event-tabs");
    $eventTab.tabs();
    $eventTab.tabs( "select", "#event-tab-"+$J(".lang_check:checked").first().data('id') );
    
    $J("#formModifyEvent").submit(function(){
        form = $J(this);
        return calendarFrontend._validation(form);
    });
    $J(".lang_check").each(function(index) {
       if (!$J(this).is(":checked")) {
           $eventTab.tabs( "disable", index );
       }
    });
    $J(".lang_check").click(function(){
        if ($J(".lang_check:checked").length < 1) {
            return false;
        }
        langIndex = $J(".lang_check").index($J(this));
        if ($J(this).is(":checked")) {
            // enable current language selection and switch to it
            $eventTab.tabs( "enable", langIndex );
            $eventTab.tabs( "select", langIndex );
        } else {
            $eventTab.tabs( "select", "#event-tab-"+$J(".lang_check:checked").first().data('id') );
            $eventTab.tabs( "disable", langIndex );
        }        
    });
    $J("#event-type").change(function(){
        $J(".event-description").hide();
        $J(".event-redirect").hide();
        if ($J(this).val() == '0') {
            $J(".event-description").show();
        } else {
            $J(".event-redirect").show();
        }
    });
    $J( ".eventLocationType" ).click(function(){
        showOrHidePlaceFields($J(this).val());
    });
    showOrHidePlaceFields($J( ".eventLocationType:checked" ).val());
});
function showOrHidePlaceFields(inputValue) {     
    if (inputValue == '1') {
        $J( "div.event_place_manual" ).css("display", "block");
        $J( "div.event_place_mediadir" ).css("display", "none");
    } else {
        $J( "div.event_place_manual" ).css("display", "none");
        $J( "div.event_place_mediadir" ).css("display", "block");
    }
}