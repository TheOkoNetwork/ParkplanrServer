{config_load file="smarty.conf"}
Hi there {$user.name}

We will soon start saving all of your Picsolve photos to your google drive account
We will send you another email when we have finished
{if $autoprocess}
In future photos will be saved daily as well as whenever any Digipass photos get auto added by {#app_full_name#}
{/if}

{include file="emails/text/footer.tpl"}
