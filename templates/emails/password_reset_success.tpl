{config_load file="smarty.conf"}

<html>
<title>Password successfully reset {#app_full_name#}</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi {$user.name}</p>
<br />
<br />




<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">The password for your {#app_full_name#} account has successfully been reset.</p>
<br/>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">If you did not reset your password please email support (parkridr@mg.okonetwork.org.uk) immediately</p>
	
<br />
<br />
<br />

{include file="emails/post_content.tpl"}
