{config_load file="smarty.conf"}

<html>
<title>MagicLink for {#app_full_name#}</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi {$user.name}</p>
<br />
<br />




<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">You recently requested a MagicLink to sign in to your {#app_full_name#} account.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Just click the button below and your in! This MagicLink is only valid for the next 24 hours.</p>
<br/>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">If you did not request a MagicLink, please ignore this email or email support (parkridr@mg.okonetwork.org.uk) if you have questions.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><b>Important!</b> Do NOT share this MagicLink with anybody, it grants access to your {#app_full_name#} account.</p>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">No one from {#app_full_name#} will ever ask for this link.</p>
	
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
	<tbody>
		<tr>
			<td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;"> <a href="{#app_url#}/magiclink/use?uid={$user.id}&token={$magiclink_token}" target="_blank" style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;">Sign me in!</a> </td>
		</tr>
	</tbody>
</table>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">If for some reason the button doesnt work please copy and paste the following url into your browser</p>
<p style="font-size:10px;"><a href="{#app_url#}/magiclink/use?uid={$user.id}&token={$magiclink_token}">{#app_url#}/magiclink/use?uid={$user.id}&token={$magiclink_token}</a></p>

<br />
<br />
<br />

{include file="emails/post_content.tpl"}
