{config_load file="smarty.conf"}

<html>
<title>Out of space on Dropbox</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi there {$user.name}</p>
<span style="font-weight: 400;">Just a heads up</span><br />
<span style="font-weight: 400;">{#app_full_name#} was unable to save your Picsolve photos to your Dropbox as Dropbox report you have insufficient space.</span><br />
<span style="font-weight: 400;">Once you have cleared enough space {#app_full_name#} will resume downloads of your photos.</span><br />
<span style="font-weight: 400;">We advise you make space for your remaining Picsolve photo's quickly as Picsolve will only keep photos for a maximum of 90 days now.</span><br />

{include file="emails/post_content.tpl"}
