{if $step === 'overview'}
    {include file='tpl_inc/model_list.tpl'
    items=$models
    includeHeader=false
    create=true
    tabs=false
    select=true
    edit=true
    search=false
    delete=true
    disable=false
    enable=false}
{elseif $step === 'detail'}
    {include file='./model_detail.tpl'
    item=$item
    arrCountries=$arrCountries
    arrExistsRedirects=$arrExistsRedirects
    includeHeader=false
    tabs=false
    saveAndContinue=true
    save=true
    cancel=true}
{/if}
