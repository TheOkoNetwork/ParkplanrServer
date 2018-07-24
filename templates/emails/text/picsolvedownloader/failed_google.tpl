{config_load file="smarty.conf"}
Hi there {$user.name}

Unfortunately we were unable to save your photos to your Google drive
Please Check to make sure you have free space available and try again.
If the error occurs again please contact support.

Google Drive returned this error:{$errorreason}

{include file="emails/text/footer.tpl"}
