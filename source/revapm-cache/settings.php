<?php

?>

<link rel="stylesheet" href="<?=MCREVAPM_PLUGIN_URL?>assets/css/font-awesome.min.css?<?=MCREVAPM_PLUGIN_VERSION?>" />
<link rel="stylesheet" href="<?=MCREVAPM_PLUGIN_URL?>assets/js/ui/jquery-ui.min.css?<?=MCREVAPM_PLUGIN_VERSION?>" />
<link rel="stylesheet" href="<?=MCREVAPM_PLUGIN_URL?>assets/css/style.css?<?=MCREVAPM_PLUGIN_VERSION?>" />
<link rel="stylesheet" href="<?=MCREVAPM_PLUGIN_URL?>assets/css/settings.css?<?=MCREVAPM_PLUGIN_VERSION?>" />

<script type="text/javascript" src="<?=MCREVAPM_PLUGIN_URL?>assets/js/ui/jquery-ui.min.js?<?=MCREVAPM_PLUGIN_VERSION?>"></script>
<script type="text/javascript" src="<?=MCREVAPM_PLUGIN_URL?>assets/js/jQuery.stringify.js?<?=MCREVAPM_PLUGIN_VERSION?>"></script>
<script type="text/javascript" src="<?=MCREVAPM_PLUGIN_URL?>assets/js/common.js?<?=MCREVAPM_PLUGIN_VERSION?>"></script>
<script type="text/javascript" src="<?=MCREVAPM_PLUGIN_URL?>assets/js/settings.js?<?=MCREVAPM_PLUGIN_VERSION?>"></script>

<style type="text/css">
</style>

<script type="text/javascript">
	var MCREVAPM_AJAX_URL = "<?=MCREVAPM_AJAX_URL?>";
</script>

<div class="wrap" id="mcrevapm_settings_div" style="display: none">
		<p>


			<h3>RevAPM Cache Settings</h3>
			<div id="mcrevapm_tabs">
				<ul>
					<li>
						<a href="#mcrevapm_tab1">
							System
						</a>
					</li>
					<li>
						<a href="#mcrevapm_tab2">
							Account
						</a>
					<li>
						<a href="#mcrevapm_tab3">
							Cache Management
						</a>
					</li>
				</ul>

				<div id="mcrevapm_tab1" class="mcrevapm_block_auto mcrevapm_tab">
					<h3>Here will be system settings</h3>
					<ul>
						<li><input type="checkbox" id="mcrevapm_cdn_status"/> RevAPM CDN Enabled</li>
					</ul>
				</div>

				<div id="mcrevapm_tab2" class="mcrevapm_block_auto mcrevapm_tab">
					<h3>Here will be account settings</h3>
					<ul>
						<li><label>Email:</label><input type="text" id="mcrevapm_email"/></li>
						<li><label>Password</label><input type="password" id="mcrevapm_pass" /></li>
						<li><button type="button" onclick="mcrevapm_create_account()">Create Account</button></li>
					</ul>
				</div>

				<div id="mcrevapm_tab3" class="mcrevapm_block_auto mcrevapm_tab">
					<h3>Here will be cache settings</h3>
					<ul>
						<li><button type="button" onclick="mcrevapm_purge_all()">Purge Cache</button></li>
					</ul>
				</div>

			</div>

	</p>
</div>