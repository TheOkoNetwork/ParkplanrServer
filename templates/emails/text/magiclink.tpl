{config_load file="smarty.conf"}
Hi {$user.name},

You recently requested a MagicLink to sign into you {#app_full_name#} account.
Just click the button below and your in! This MagicLink is only valid for the next 24 hours.

If you did not request a MagicLink, please ignore this email or email support (parkridr@mg.okonetwork.org.uk) if you have questions.

Important! Do NOT share this MagicLink with anybody, it grants access to your {#app_full_name#} account. 
No one from {#app_full_name#} will ever ask for this link.

{#app_url#}/magiclink/use?uid={$user.id}&token={$magiclink_token}

{include file="emails/text/footer.tpl"}
