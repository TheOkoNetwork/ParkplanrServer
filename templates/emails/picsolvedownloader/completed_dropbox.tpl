{config_load file="smarty.conf"}

<html>
<title>Finished saving to your Dropbox</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi there {$user.name}</p>
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">We have finished saving all of your Picsolve photos to Dropbox</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><a href="https://www.dropbox.com/home/Apps/ParkPlanr">Open folder</a></p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">It may take a few minutes for Dropbox to finish processing the upload before they will appear.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">{$new_count} photos were saved.</p>
{if $failed_image_count ne 0}
	<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Unfortunately {$failed_image_count} photos could not be saved due to an error on Picsolves system.</p>
{/if}
<br />
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Thank you very much for using {#app_full_name#}!</p>

{include file="emails/post_content.tpl"}
