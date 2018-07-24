{config_load file="smarty.conf"}

<html>
<title>Whoops! Something went wrong saving to your Google Drive</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi there {$user.name}</p>
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Unfortunately we were unable to save your photos to your Google drive</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Please Check to make sure you have free space available and try again.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">If the error occurs again please contact support.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Google Drive returned this error:{$errorreason}</p>
<br />
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Thank you very much for using {#app_full_name#}!</p>

{include file="emails/post_content.tpl"}
