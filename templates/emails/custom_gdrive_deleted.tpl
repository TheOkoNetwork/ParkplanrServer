{config_load file="smarty.conf"}

<html>
<title>Unable to save to Google Drive</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi there {$user.name}</p>
<span style="font-weight: 400;">Just a heads up</span><br />
<span style="font-weight: 400;">{#app_full_name#} is unable to save your Picsolve photos to your Google Drive account as Google reports your Google account has been deleted.</span><br />
<span style="font-weight: 400;">Please visit <a href="{#app_url#}/app">{#app_url#}/app</a> then click Picsolve Downloader, to link a new Google account or a dropbox account instead.</span><br />
<span style="font-weight: 400;">Your Picsolve photos will NOT automatically be saved by {#app_url#} until you link another account, please ensure you save your photo's before Picsolve's 90 day deadline.</span><br />
<span style="font-weight: 400;">Any questions about this? Feel free to reply to this email.</span><br />

{include file="emails/post_content.tpl"}
