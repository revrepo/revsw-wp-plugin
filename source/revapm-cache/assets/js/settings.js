var mcrevapm_domain = window.location.hostname;
var mcrevapm_counter = 0;
var mcrevapm_add_domain_started = false;

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
						// error
						jQuery("#mcrevapm_tab2 .mcrevapm_err").html(res.message).show();
					}
					else {
						// ok
						jQuery("#mcrevapm_signup").find('input[type="text"],input[type="email"],input[type="password"],select').prop("disabled",true);
						jQuery("#mcrevapm_tab2 .mcrevapm_ok").html(res.message).show();
						jQuery(el).addClass("mcrevapm_hidden");
						jQuery(el).next().removeClass("mcrevapm_hidden");
					}
				} catch(e) { if (console) console.log(e.message); }
      }
	});
}

function mcrevapm_get_api_key(el){
	jQuery(el).prop("disabled",true);
	jQuery(el).find(".mcrevapm_spinner").removeClass("mcrevapm_hidden");
	jQuery("#mcrevapm_tab2 .mcrevapm_err").hide();
	jQuery("#mcrevapm_tab2 .mcrevapm_ok").hide();
	jQuery.post( MCREVAPM_AJAX_URL,{
		"action": "mcrevapmgetapikey",
		"email": ""+jQuery("#mcrevapm_email").val(),
		"pass": ""+jQuery("#mcrevapm_pass").val()
	}, function(res) {
//console.log(res);
		jQuery(el).prop("disabled",false);
		jQuery(el).find(".mcrevapm_spinner").addClass("mcrevapm_hidden");
//console.log(res); console.log("statusCode = "+parseInt(res.statusCode) );
		if (parseInt(res.statusCode) != 200) {
			// error
			jQuery("#mcrevapm_tab2 .mcrevapm_err").html(res.message).show();
		} else {
			jQuery("#mcrevapm_tab2 .mcrevapm_ok").html(res.message).show();
			jQuery(el).hide();
			window.location.reload();
		}
	});
}

function mcrevapm_update_api_key(){
	var new_key = jQuery("#mcrevapm_api_key").val().toString();
	var old_key;
	try{
		old_key = MCREVAPM_SETTINGS.api_key.toString();
	} catch(e) { if (console) console.log("Error getting old api key\n"+e.message); return; }
//console.log("new_key = "+new_key);console.log("old_key = "+old_key);
	if (old_key == new_key) return;
	if (jQuery("#mcrevapm_api_key_updating_div").hasClass("mcrevapm_hidden")) {
//console.log("has class - starting...");
		jQuery("#mcrevapm_api_key_updating_div").removeClass("mcrevapm_hidden");
		if (confirm("Update API Key. Are you sure?")) {
//console.log("has class - starting...confirmed!");
			jQuery.post(MCREVAPM_AJAX_URL,{
				"action": "mcrevapmsetapikey",
				"key":    new_key
			}, function (res){
//console.log(res);
				try{
					jQuery("#mcrevapm_api_key_updating_div").addClass("mcrevapm_hidden");
					if (parseInt(res.statusCode) != 200) {
						alert(res.message);
						jQuery("#mcrevapm_api_key").val(old_key);
						return;
					}
					jQuery("#mcrevapm_cdn_status").prop("checked",false);
					// update domains list
					jQuery("#mcrevapm_domains_table tbody").html("");
					for (var key in res.data) {
						var d = res.data[key];
						jQuery("#mcrevapm_domains_table tbody").append('<tr id="mcrevapm_domain_row_'+d.id+'" data-id="'+d.id+'">'
								+'<td><input type="checkbox" name="mcrevapm_rg_domains" class="mcrevapm_rg_domains" /></td>'
								+'<td>'+d.domain_name+'</td>'
								+'<td>'+d.origin_server+'</td>'
								+'<td>'+d.origin_host_header+'</td>'
								+'<td>'+MCREVAPM_LOCATIONS_DATA[d.location_id]+'</td>'
								+'<td>'+d.cname+'</td>'
								+'<td>'
//									+'<i class="fa fa-pencil mcrevapm_edit_domain" aria-hidden="true" onclick="mcrevapm_edit_domain(this)" title="Edit Domain"></i>'
									+'<i class="fa fa-times mcrevapm_delete_domain" aria-hidden="true" onclick="mcrevapm_delete_domain(this)" title="Delete Domain"></i>'
								+'</td>'
								+'</tr>'
							);
					}
				} catch(e) { if (console) console.log("Update API key error:\n"+e.message); jQuery("#mcrevapm_api_key").val(old_key); }
			});
		} else {
			jQuery("#mcrevapm_api_key").val(old_key);
			jQuery("#mcrevapm_api_key_updating_div").addClass("mcrevapm_hidden");
			return;
		}
	}
}

function mcrevapm_add_domain(){
	mcrevapm_counter++;
	var id='mcrevapm_domain_row_'+mcrevapm_counter;
	jQuery("#mcrevapm_domains_table tbody").append('<tr id="'+id+'" data-id="" >'
		+'<td><input type="checkbox" name="mcrevapm_rg_domains" class="mcrevapm_rg_domains" disabled="disabled" /></td>'
		+'<td><input type="text" class="mcrevapm_domain" value="'+mcrevapm_domain+'"/></td>'
		+'<td><input type="text" class="mcrevapm_server" value="'+mcrevapm_domain+'"/></td>'
		+'<td><input type="text" class="mcrevapm_header" value="'+mcrevapm_domain+'"/></td>'
		+'<td><select class="mcrevapm_location">'+MCREVAPM_LOCATIONS_HTML+'</select></td>'
		+'<td>&nbsp;</td>'
		+'<td>'
			+'<i class="fa fa-check mcrevapm_submit_domain" aria-hidden="true" onclick="mcrevapm_submit_add_domain(this)" title="Save New Domain"></i>'
			+'<i class="fa fa-times mcrevapm_delete_domain" aria-hidden="true" onclick="mcrevapm_delete_domain(this)" title="Delete Domain"></i>'
		+'</td>'
		+'</tr>'
	);
}

function mcrevapm_submit_add_domain_wait(domain_name){
	if (!mcrevapm_add_domain_started)  return;
	jQuery.post(MCREVAPM_AJAX_URL,{"action": "mcrevapmgetdomains"}, function(res){
		try {
//console.log(res);
			var found = -1;
			for (var i = 0; i < res.length; i++) {
				if (domain_name.toString() == domain_name) {
					found = i;
					break;
				}
			}
//console.log("domain_name = "+domain_name+" found = "+found);
			if (found < 0)
				setTimeout("mcrevapm_submit_add_domain_wait('"+domain_name+"')",10000);
			else {
				setTimeout("window.location.reload()",20000);
			}
		} catch(e) { if (console) console.log("Error in get_domains:\n"+e.message); window.location.reload();}
	});
}

function mcrevapm_submit_add_domain(el){
	var loc = ""+jQuery(el).parents("tr").find(".mcrevapm_location").val();
//console.log("loc = "+loc);
	if ((loc.length <= 0) || (loc == "null") || (loc == "undefined")) {
		alert("You should select a location");
		return;
	}
	var request = {
		"action": "mcrevapmadddomain",
		"domain": ""+jQuery(el).parents("tr").find(".mcrevapm_domain").val(),
		"server": ""+jQuery(el).parents("tr").find(".mcrevapm_server").val(),
		"header": ""+jQuery(el).parents("tr").find(".mcrevapm_header").val(),
		"location": ""+jQuery(el).parents("tr").find(".mcrevapm_location").val()
	};
	if ((request.domain.length <= 0) || (request.domain == "null") | (request.domain == "undefined")) {
		return;
	}
	jQuery(el).parents("tr").find("td:last").append(jQuery("#mcrevapm_domain_progress_container").html());
	mcrevapm_add_domain_started = true;
//console.log("add domain"); console.log(request);
	jQuery.post(MCREVAPM_AJAX_URL,request, function(res){
		if (parseInt(res.statusCode) >= 400) {
			jQuery(el).parents("tr").find(".mcrevapm_domain_progress").remove();
			mcrevapm_add_domain_started = false;
			alert(res.message);
		}
	});
	mcrevapm_submit_add_domain_wait(request.domain);
	//alert("submit add");
}

function mcrevapm_delete_domain(el){
	if (!confirm("Delete domain. Are you sure?")) return;
	var id = "" + jQuery(el).parents("tr").attr("data-id");
	if (id.length < 5) {
		jQuery(el).parents("tr").remove();
		return;
	}
	var request = {
		"action": "mcrevapmadeletedomain",
		"id": id
	};
//console.log("delete domain id = "+id);
	jQuery.post(MCREVAPM_AJAX_URL,request, function(res){
//console.log(res);
		if (parseInt(res.statusCode) != 200) {
			alert(res.message);
		} else {
			jQuery(el).parents("tr").remove();
		}
	});
}

/*
function mcrevapm_edit_domain(el){
	var id = "" + jQuery(el).parents("tr").attr("data-id");
	alert("edit" + id);
}
*/

function mcrevapm_save_system_settings(){
	//console.log("save system settings");
	if (!jQuery("#mcrevapm_cdn_enabled_progress_div").hasClass("mcrevapm_hidden")) return;
	if (jQuery("#mcrevapm_cdn_status").is(":checked")) {
		var qty = jQuery(".mcrevapm_rg_domains:checked").length;
//console.log("checked = "+qty);
		if (qty <= 0) {
			jQuery("#mcrevapm_cdn_status").prop("checked",false);
			alert("You should select at least one domain to use RevAPM CDN");
			return;
		}
	}
	var ids = [];
	jQuery(".mcrevapm_rg_domains:checked").each(function (){
		var domain_id = ""+jQuery(this).parents("tr").attr("data-id");
		if (domain_id.length > 5)
			ids.push(domain_id);
	});
	jQuery("#mcrevapm_cdn_enabled_progress_div").removeClass("mcrevapm_hidden");
	var request = {
		"cdn_status": (jQuery("#mcrevapm_cdn_status").is(":checked")) ? "on" : "off",
		"ids": ids
	};
//console.log(request);
	jQuery.ajax({
		  type:             'POST',
      url:              MCREVAPM_AJAX_URL+"?action=mcrevapmsavesystemsettings",
		  contentType:      'application/json',
      data:             JSON.stringify(request),
      dataType:         "json",
      success: function(res){
//console.log(res);
	      try{
					jQuery("#mcrevapm_cdn_enabled_progress_div").addClass("mcrevapm_hidden");
		      if (res.statusCode != 200)
		      	alert(res.message);
		      else {
						jQuery("#mcrevapm_cdn_status").prop("checked", (res.data.cdn_status == "on"));
			      alert("System savings saved!");
		      }
				} catch(e) { if (console) console.log(e.message); }
      }
	});

}

function mcrevapm_purge_all(){
	alert ("Not implemented yet")
}


jQuery(document).ready(function(){
//console.log("ready settings page"); console.log(MCREVAPM_SETTINGS);
		var key = "";
		if (MCREVAPM_SETTINGS.hasOwnProperty("api_key"))
			key += MCREVAPM_SETTINGS.api_key;
//console.log(key);
		if (key.length > 10) {
	  jQuery("#mcrevapm_tabs").tabs({
        "active": 0
    });
		} else {
		  jQuery("#mcrevapm_tabs").tabs({
	        "active": 1
	    });
		}
		jQuery("#mcrevapm_settings_div").show();
		jQuery("#mcrevapm_plan").html("");
		for (var key in MCREVAPM_BILLING_PLANS) {
			var plan = MCREVAPM_BILLING_PLANS[key];
			var name = "" + plan.name + " $" + plan.monthly_fee+"/mon";
			jQuery("#mcrevapm_plan").append('<option value="'+key+'">'+name+'</option>');
		}
		jQuery("#mcrevapm_plan").on("change",function(){
			var key = "" + jQuery(this).val();
			try{
				var plan = MCREVAPM_BILLING_PLANS[key];
				jQuery(".mcrevapm_plan_description").html(plan.description);
				jQuery(".mcrevapm_plan_price").html(plan.monthly_fee);
				jQuery(".mcrevapm_plan_price1").html(plan.services[0].cost);
				jQuery(".mcrevapm_plan_unit1").html(plan.services[0].measurement_unit);
				jQuery(".mcrevapm_plan_price2").html(plan.services[1].cost);
				jQuery(".mcrevapm_plan_unit2").html(plan.services[1].measurement_unit);
				jQuery(".mcrevapm_plan_price3").html(plan.services[2].cost);
				jQuery(".mcrevapm_plan_unit3").html(plan.services[2].measurement_unit);
				jQuery(".mcrevapm_plan_price4").html(plan.services[3].cost);
				jQuery(".mcrevapm_plan_unit4").html(plan.services[3].measurement_unit);
				jQuery(".mcrevapm_plan_price5").html(plan.services[4].cost);
				jQuery(".mcrevapm_plan_unit5").html(plan.services[4].measurement_unit);
			} catch(e) { if (console) console.log("Error in setting plan key = "+key+"\n"+e.message);}
		}).trigger("change");
		jQuery("#mcrevapm_api_key").bind("propertychange blur paste", function (){
//console.log("api key changed");
			mcrevapm_update_api_key();
		}).on("keypress",function (e){
			if ( e.which == 13 ) {
        e.preventDefault();
				mcrevapm_update_api_key();
      }
		});
	jQuery("#mcrevapm_cdn_status").on("click",function(e){
		mcrevapm_save_system_settings();
	});
	jQuery(".mcrevapm_rg_domains").on("click",function(e){
		jQuery("#mcrevapm_cdn_status").prop("checked",false).trigger("click");
	});
});