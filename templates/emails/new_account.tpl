{config_load file="smarty.conf"}

<html>
<title>Welcome to {#app_full_name#}</title>
{include file="emails/style.tpl"}
{include file="emails/pre_content.tpl"}

<p style="font-family: sans-serif; font-size: 32px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Welcome to {#app_full_name#} {$user.name}!</p>
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Please verify your email address by clicking the button below</p>
	
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
	<tbody>
		<tr>
			<td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;"> <a href="{#app_url#}/verifyemail?uid={$user.id}&token={$user.email_verification_token}" target="_blank" style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;">Verify my email address</a> </td>
		</tr>
	</tbody>
</table>
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">If for some reason the button doesnt work please copy and paste the following url into your browser</p>
<p style="font-size:10px;"><a href="{#app_url#}/verifyemail?uid={$user.id}&token={$user.email_verification_token}">{#app_url#}/verifyemail?uid={$user.id}&token={$user.email_verification_token}</a></p>

<br />
<br />
<br />
<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Thank you very much for joining us!</p>

{include file="emails/post_content.tpl"}
