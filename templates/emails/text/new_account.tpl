{config_load file="smarty.conf"}
Welcome to {#app_full_name#} {$user.name}!

To verify your account please copy and paste the following url into your browser
{#app_url#}/verifyemail?uid={$user.id}&token={$user.email_verification_token}

{include file="emails/text/footer.tpl"}
