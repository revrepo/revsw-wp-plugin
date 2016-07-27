function mcrevapm_save_settings(){
	console.log("save settings");
}

function mcrevapm_create_account(el){
	jQuery(el).prop("disabled",true);
	jQuery(el).find(".mcrevapm_spinner").removeClass("mcrevapm_hidden");
	jQuery("#mcrevapm_tab2 .mcrevapm_err").hide();
	jQuery("#mcrevapm_tab2 .mcrevapm_ok").hide();
	var json_data = mcrevapm_save_html_form_data("mcrevapm_signup");
	jQuery.ajax({
		  type:             'POST',
      url:              MCREVAPM_AJAX_URL+"?action=mcrevapmsignup",
		  contentType:      'application/json',
      data:             JSON.stringify(json_data),
      dataType:         "json",
      success: function(res){
//console.log(res);
	      try{
					jQuery(el).prop("disabled",false);
					jQuery(el).find(".mcrevapm_spinner").addClass("mcrevapm_hidden");
					if (parseInt(res.statusCode) != 200) {
						jQuery("#mcrevapm_tab2 .mcrevapm_err").html(res.message).show();
					}
					else {
						jQuery("#mcrevapm_tab2 .mcrevapm_ok").html(res.message).show();
						jQuery(el).addClass("mcrevapm_hidden");
						jQuery(el).next().removeClass("mcrevapm_hidden");
					}
				} catch(e) { if (console) console.log(e.message); }
      }
	});
}

function mcrevapm_get_api_key(){
	alert("Not implemented yet!");
}

function mcrevapm_purge_all(){

}

jQuery(document).ready(function(){
console.log("ready settings page"); console.log(MCREVAPM_SETTINGS);
	  jQuery("#mcrevapm_tabs").tabs({
        "active": 1
    });
		jQuery("#mcrevapm_settings_div").show();
		jQuery("#mcrevapm_plan").on("change",function(){
			jQuery(".mcrevapm_plan").addClass("mcrevapm_hidden");
			var plan = ""+jQuery(this).val();
			jQuery(".mcrevapm_plan_"+plan).removeClass("mcrevapm_hidden");
		});
});