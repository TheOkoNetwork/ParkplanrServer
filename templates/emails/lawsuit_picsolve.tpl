{config_load file="smarty.conf"}

<html>
<title>Important information about {#app_full_name#}</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi there {$user.name}</p>
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Unfortunately we have had to remove all Picsolve functionality from {#app_full_name#} permanently.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">ParkPlanr will remain for queue times,maps,ridecounts discount calculator and a few cool features planned.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">We are aware that most ParkPlanr users used ParkPlanr solely for Picsolve related functions, if you wish to close your ParkPlanr account please reply to this email. </p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">We hope you remain using ParkPlanr's other functions/features including some new ones coming soon.</p>
<br />
<br />

{include file="emails/post_content.tpl"}
