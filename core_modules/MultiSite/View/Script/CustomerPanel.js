var websiteLoginUrl;
var cadminPath;
cx.ready(function() {
    cadminPath      = cx.variables.get('cadminPath', 'contrexx');
    websiteLoginUrl = cadminPath + 'index.php&cmd=JsonData&object=MultiSite&act=websiteLogin';    
});

var customerPanel = {
  messageTypes : ['error', 'warning', 'info', 'success']  
};

/**
 * Show messages to the user
 * uses bootstrap modal and predefined modal boxes
 * 
 * @param html   msgTxt Html format message text
 * @param string type   Type of message(error, warning, info, success)
 */
function showMessage(msgTxt, type) {
    type      = jQuery.inArray(type, customerPanel.messageTypes) !== -1 ? type : 'error';
    $objModal = jQuery('#'+ type + '_msg_container');
    $content  = $objModal.find('.msg_text');
    
    $content.html(msgTxt);
    $objModal.modal('show');
}

function getQueryParams(qs) {
    qs = qs.split("+").join(" ");
    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}
    
function  loadContent(jQuerySelector, url) {
    jQuery.ajax({
        dataType: 'html',
        url: url,
        type: 'GET',
        success: function(data) {
            if (data) {
                jQuery(jQuerySelector).html(data);
            }
        },
        fail: function(data) {}
    });
}

/**
 * Generate Remote website-login token
 * depends bootstrap js
 * 
 * @param object elm jQuery button object
 */
function getRemoteLoginToken($this) {
    var websiteId = $this.data('id');
    
    jQuery.ajax({
        dataType: "json",
        url: websiteLoginUrl,
        data: {
            websiteId :  websiteId
        },
        type: "POST",
        beforeSend: function (xhr, settings) {
            $this.button('loading');
            $this.prop('disabled', true);
        },
        success: function(response) {
            if (response.status == 'success') {
                resp = response.data;
                if (resp.status == 'success') {
                    var newWindow = window.open(resp.webSiteLoginUrl, '_blank');
                    if(newWindow) {
                        //Browser has allowed it to be opened
                        newWindow.focus();
                    }
                } else {
                    showMessage(resp.message, 'error');
                }
            } else {
                showMessage(response.message, 'error');
            }
        },
        complete: function (xhr, settings) {
            $this.button('reset');
            $this.prop('disabled', false);
        },
        error: function() { }
    });
    
    /**
     * Enable | Disable mail service
     * 
     * @param object elm jQuery button object
     */
    function enableOrDisableMailService($this) {
        var act = $this.data('act');
        var url = cadminPath + 'index.php&cmd=JsonData&object=MultiSite&act='+act;    
        var websiteId = $this.data('id');
        var message = '';

        jQuery.ajax({
            dataType: "json",
            url: url,
            data: {
                websiteId :  websiteId
            },
            type: "POST",
            beforeSend: function (xhr, settings) {
                $this.button('loading');
                $this.prop('disabled', true);
            },
            success: function(response) {
                if (response.status == 'success') {
                    resp = response.data;
                    if (resp.status == 'success') {
                        showMessage(resp.message, 'success');
                        loadContent('#multisite_website_email', '/api/MultiSite/Website/Email?id=' + websiteId);
                    } else {
                        showMessage(resp.message, 'error');
                    }
                } else {
                    showMessage(response.message, 'error');
                }
            },
            complete: function (xhr, settings) {
                $this.button('reset');
                $this.prop('disabled', false);                
            },
            error: function() { }
        });
    }
    
    function pleskAutoLogin($this) {
        var url = cadminPath + 'index.php&cmd=JsonData&object=MultiSite&act=pleskAutoLoginUrl';    
        var websiteId = $this.data('id');

        jQuery.ajax({
            dataType: "json",
            url: url,
            data: {
                websiteId :  websiteId
            },
            type: "POST",
            beforeSend: function (xhr, settings) {
                $this.button('loading');
                $this.prop('disabled', true);
            },
            success: function(response) {
                if (response.status == 'success') {
                    switch(response.data.status) {
                        case 'success':
                            window.open(response.data.pleskAutoLoginUrl, '_blank');
                            break;
                        case 'error':
                            showMessage(response.data.message, 'error');
                            break;
                        default:
                            break;
                    }
                } else {
                    showMessage(response.message, 'error');
                }
            },
            complete: function (xhr, settings) {
                $this.button('reset');
                $this.prop('disabled', false);                
            },
            error: function() { }
        });
    }
}

function showAddNewWebsite(remoteUrl) {
  if (!jQuery('#SubscriptionAddWebsite').length) {
    return;
  }
  
  jQuery('#SubscriptionAddWebsite .modal-content')
    .html(
      jQuery('<div />')
        .addClass('grid-elm grid-align-1-1 grid-offset')
        .html('<img src="/lib/javascript/jquery/jstree/themes/default/throbber.gif" /> Loading')
    );
  jQuery('#SubscriptionAddWebsite')
    .on("hidden.bs.modal",function() {
      signUpForm = jQuery('#multisite_signup_form');
      signUpModal = signUpForm.parents('.modal');
      signUpModal.unbind();
      jQuery(this).data('bs.modal', null);
    }).modal({
      remote : remoteUrl
    });
}