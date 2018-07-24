{config_load file="smarty.conf"}

<html>
<title>We'll soon be saving your Picsolve photos to your Google Drive</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi there {$user.name}</p>
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">We will soon start saving all of your Picsolve photos to your Google Drive account</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">We will send you another email when we have finished</p>
{if $autoprocess}
        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">In future photos will be saved daily as well as whenever any Digipass photos get auto added by {#app_full_name#}</p>
{/if}
<br />
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Thank you very much for using {#app_full_name#}!</p>

{include file="emails/post_content.tpl"}
