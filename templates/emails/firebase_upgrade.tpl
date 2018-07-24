{config_load file="smarty.conf"}

<html>
<title>Important information about your {#app_full_name#} account</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi there {$user.name}</p>
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">We are currently undertaking a major upgrade of {#app_full_name#} to add some exciting new features.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">As part of this upgrade we are updating our user accounts system.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">To complete the upgrade of your account please <a href="{#app_url#}/signin">sign in to {#app_full_name#}</a></p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">If the link above didn't work please copy and paste the following address into your browser {#app_url#}/signin</p>
{if $user.digipasses}
	<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">So that you know this is a real email below are the Digipass barcodes for any Digipasses on your account.</p>
	{foreach from=$user.digipasses item=digipass}
		<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Barcode:{$digipass.barcode}</p>
	{/foreach}
{/if}
<br />
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Thank you very much!</p>

{include file="emails/post_content.tpl"}
