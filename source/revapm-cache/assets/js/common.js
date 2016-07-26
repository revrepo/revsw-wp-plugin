var mcrevapm_dialog = null;

Number.prototype.toFixedDown = function(digits) {
    var re = new RegExp("(\\d+\\.\\d{" + digits + "})(\\d)"),
        m = this.toString().match(re);
    return m ? parseFloat(m[1]) : this.valueOf();
};

function mcrevapm_createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    } else var expires = "";
    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
}

function mcrevapm_readCookie(name) {
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length, c.length));
    }
    return "";
}

function mcrevapm_eraseCookie(name) {
    mcrevapm_createCookie(name, "", -1);
}

function mcrevapm_d(msg){
    if (console) console.log(msg);
}

function mcrevapm_apply_error_dialog_css(){
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-content').css({"color" : "#000"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-header').css({"background" : "#ff0000", "color" : "#fff"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-content').find('.ui-dialog-buttonset button').eq(0).css({"background" : "#ff0000", "color" : "#ffffff"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-dialog-titlebar-close').html("X").css({"color": "#ff0000"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").css({"z-index": 10000});
}

function mcrevapm_apply_success_dialog_css(){
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-content').css({"color" : "#000"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-header').css({"background" : "#008000", "color" : "#fff"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-content').find('.ui-dialog-buttonset button').eq(0).css({"background" : "#008000", "color" : "#ffffff"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-dialog-titlebar-close').html("X").css({"color": "#008000"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").css({"z-index": 10000});
}

function mcrevapm_apply_confirmation_dialog_css(){
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-content').css({"color" : "#000"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-header').css({"background" : "#0000ff", "color" : "#fff"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-widget-content').find('.ui-dialog-buttonset button').eq(0).css({"background" : "#0000ff", "color" : "#ffffff"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").find('.ui-dialog-titlebar-close').html("X").css({"color": "#008000"});
    jQuery(mcrevapm_dialog).parents("div.ui-dialog").css({"z-index": 10000});
}

function mcrevapm_show_error_dialog(message) {
    // show error dialog
    mcrevapm_dialog =  jQuery( "#mcrevapm_error_dialog" ).dialog({
        modal       : true
        , buttons: [
            {
                text: mcrevapm_lang_t("close"), click: function() { jQuery(this).dialog( "close" ); }
            }
        ]
    });
    jQuery( "#mcrevapm_error_dialog_header").html(message);
    mcrevapm_apply_error_dialog_css();
}

function mcrevapm_show_success_dialog(message) {
    // show success dialog
    mcrevapm_dialog =  jQuery( "#mcrevapm_success_dialog" ).dialog({
        modal       : true
        , buttons: [
            {
                text: mcrevapm_lang_t("close"), click: function() { jQuery(this).dialog( "close" ); window.location.reload(); }
            }
        ]
    });
    jQuery( "#mcrevapm_success_dialog_header").html(message);
    mcrevapm_apply_success_dialog_css();
}

function mcrevapm_is_number_input(el){
    if (!el) return false;
//console.log(el);
    var tag = jQuery(el).prop("tagName");
    var type = jQuery(el).attr("type");
    var res = ((tag == "INPUT") && (type == "number"))
//console.log("tag = "+tag+" type = "+type+" res = "+res);
    return res;
}

function mcrevapm_is_password_input(el){
    if (!el) return false;
//console.log(el);
    var tag = jQuery(el).prop("tagName");
    var type = jQuery(el).attr("type");
    var res = ((tag == "INPUT") && (type == "password"))
//console.log("tag = "+tag+" type = "+type+" res = "+res);
    return res;
}

function mcrevapm_is_text_input(el){
    if (!el) return false;
//console.log(el);
    var tag = jQuery(el).prop("tagName");
    var type = jQuery(el).attr("type");
    var res = ((tag == "INPUT") && (type == "text"))
//console.log("tag = "+tag+" type = "+type+" res = "+res);
    return res;
}

function mcrevapm_is_checked_input(el){
    if (!el) return false;
//console.log(el);
    var tag = jQuery(el).prop("tagName");
    var type = jQuery(el).attr("type");
    var res = ((tag == "INPUT") && (type == "checkbox"))
//console.log("tag = "+tag+" type = "+type+" res = "+res);
    return res;
}

function mcrevapm_is_radio_input(el){
    var tag = jQuery(el).prop("tagName");
    var type = jQuery(el).attr("type");
    var res = ((tag == "INPUT") && (type == "radio"))
//console.log("tag = "+tag+" type = "+type+" res = "+res);
    return res;
}


function mcrevapm_is_tinymce(el){
    var tag = jQuery(el).prop("tagName");
    var display = jQuery(el).css('display');
//console.log("id = "+jQuery(el).attr("id")); console.log("tag = "+tag);  console.log("display = "+display); console.log("res = "+((tag == "textarea") && (display == "none")));
    return (tag == "TEXTAREA") && (display == "none");
}

function mcrevapm_is_select(el){
    var tag = jQuery(el).prop("tagName");
    return (tag == "SELECT");
}

function mcrevapm_is_textarea(el){
    var tag = jQuery(el).prop("tagName");
    return (tag == "TEXTAREA");
}

function mcrevapm_is_datepicker(el){
    return jQuery(el).hasClass("hasDatepicker");
}