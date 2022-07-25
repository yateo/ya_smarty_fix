<div id="ya_app" class="container">
    {if !empty($message)}
        <div class="module_confirmation conf confirm alert alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {if !empty($message_deleted)}
                <p>{$message_deleted}</p>
                <form action="{$moduleUrl|escape:'htmlall':'UTF-8'}" method="POST" name="form_delete">
                    <button type="submit" class="btn btn-danger" name="delete" value="1">{l s='Uninstall module' mod='ya_smarty_fix'}</button>
                </form>
            {/if}
            {if !empty($message_nothing)}
                <p>{$message_nothing}</p>
            {/if}
        </div>
    {/if}

    <div class="panel">
        <h3>{l s='General Settings' mod='ya_smarty_fix'}</h3>

        <div class="row m-0 p-0">
            <p>{l s='By clicking on "fix", you\'ll fix your shop by removing the Smarty\'s Hack Gate referred to' mod='ya_smarty_fix'} : <a href="https://build.prestashop.com/news/major-security-vulnerability-on-prestashop-websites/" target="_blank">PrestaShop Major Vulnerabily Link</a></p>
        </div>

        <div class="panel-footer">
            <form action="{$moduleUrl|escape:'htmlall':'UTF-8'}" method="POST" name="form_fix">
                <button type="submit" class="btn btn-default pull-right" name="fix_it" value="1">{l s='Fix it' mod='ya_smarty_fix'}</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <h3>{l s='File' mod='ya_smarty_fix'}</h3>

        <div class="row m-0 p-0">
            {$fileRead nofilter}
        </div>
    </div>
</div>

<style>
    #ya_app .material-icons { vertical-align: middle; }
    #ya_app h3 { padding: 16px !important; height: 60px !important; font-weight: bold !important;}
    #ya_app p { font-size: 14px; padding: 15px; }
    #ya_app button { padding: 15px 20px; }
</style>
