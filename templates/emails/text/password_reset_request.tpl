{config_load file="smarty.conf"}
Hi {$user.name},

You recently requested to reset your password for your {#app_full_name#} account.
Use the url below to reset it. This password reset is only valid for the next 24 hours.

If you did not request a password reset, please ignore this email or email support (parkridr@mg.okonetwork.org.uk) if you have questions.

{#app_url#}/resetpassword?uid={$user.id}&token={$password_reset_token}

{include file="emails/text/footer.tpl"}
