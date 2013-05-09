[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<script type="text/javascript">
<!--
function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
}
//-->
</script>

[{ if $readonly }]
[{assign var="readonly" value="readonly disabled"}]
[{else}]
[{assign var="readonly" value=""}]
[{/if}]
<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]    
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="toxid_setup_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>
<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="toxid_setup_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="language" value="[{ $actlang }]">
    <div class="groupExp">
        <div class="exp">
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident='TOXID_SUPPORT_HEADLINE'}]</b></a>
            <dl>
                <dd>
                    <table>
                        <tr>
                            <td valign="top" style="width:500px;padding-right:30px;">
                                <a href="http://www.marmalade.de/?pk_campaign=toxidBackend&pk_kwd=Imagelink" target="_blank" style="float:left;margin:0 20px 10px 0">
                                    <img src="../modules/toxid_curl/toxid.jpg" width="90px" />
                                </a>
                                [{oxmultilang ident='TOXID_SUPPORT_DESC'}]
                                <br />
                                <br />
                                <br />
                                <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=shop%40marmalade%2ede&lc=DE&item_name=marmalade%20GmbH&item_number=TOXID%20Support&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest" target="_blank">
                                    <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_SM.gif">
                                </a>
                            </td>
                            <td valign="top" style="padding-right:30px">
                                <b>[{oxmultilang ident='TOXID_INTEGRATIONPARTNER'}]</b>
                                <ul>
                                    <li><a href="http://www.marmalade.de/?pk_campaign=toxidBackend&pk_kwd=Integrationpartner" target="_blank">marmalade GmbH</a></li>
                                    <li><a href="http://www.gn2-netwerk.de/" target="_blank">GN2 Netwerk</a></li>
                                    <li><a href="http://www.marmalade.de/agentur/agentur/impressum/?pk_campaign=toxidBackend&pk_kwd=IntegrationpartnerGesucht" target="_blank">[{oxmultilang ident='TOXID_BECOME_PARTNER'}]</a></li>
                                </ul>
                            </td>
                            <td valign="top" style="padding-right:30px">
                                <b>Contributors</b>
                                <ul>
                                    <li><a href="https://github.com/dajoho" target="_blank">dajoho</a></li>
                                    <li><a href="https://github.com/vanilla-thunder" target="_blank">vanilla-thunder</a></li>
                                    <li><a href="https://github.com/Tr0nYx" target="_blank">Tr0nYx</a></li>
                                    <li><a href="https://github.com/pixelkrieg" target="_blank">pixelkrieg</a></li>
                                    <li><a href="https://github.com/wanis" target="_blank">wanis</a></li>
                                </ul>
                            </td>
                            <td valign="top">
                                <b>Supporter</b>
                                <ul>
                                    <li>[{oxmultilang ident='TOXID_LOOKING_FOR'}]</li>
                                </ul>
                            </td>
                         </tr>
                    </table>
                </dd>
            </dl>
        </div>
    </div>
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident='toxid_setup'}]</b></a>
            <dl>
                <dd>
                    [{foreach from=$languages key=lang item=olang}]
                        <fieldset>
                            <legend>[{ $olang->name }]</legend>
                            <table>
                                <tr>
                                    <td valign="top" class="edittext">
                                        [{oxmultilang ident="TOXID_SOURCE"}]: 
                                    </td>
                                    <td valign="top" class="edittext">
                                            <input type="text" name="editval[aToxidCurlSource][[{ $lang }]]" value="[{$aToxidCurlSource.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">
                                        [{oxmultilang ident="TOXID_SOURCE_SSL"}]: 
                                    </td>
                                    <td valign="top" class="edittext">
                                            <input type="text" name="editval[aToxidCurlSourceSsl][[{ $lang }]]" value="[{$aToxidCurlSourceSsl.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">
                                        [{oxmultilang ident="TOXID_SEARCH_URL"}]: 
                                    </td>
                                    <td valign="top" class="edittext">
                                            <input type="text" name="editval[aToxidSearchUrl][[{ $lang }]]" value="[{$aToxidSearchUrl.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">
                                        [{oxmultilang ident="TOXID_PARAM"}]: 
                                    </td>
                                    <td valign="top" class="edittext">
                                            <input type="text" name="editval[aToxidCurlUrlParams][[{ $lang }]]" value="[{$aToxidCurlUrlParams.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">
                                        [{oxmultilang ident="TOXID_SEO_SNIPPET"}]: 
                                    </td>
                                    <td valign="top" class="edittext">
                                            <input type="text" name="editval[aToxidCurlSeoSnippets][[{ $lang }]]" value="[{$aToxidCurlSeoSnippets.$lang}]">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    [{/foreach}]
                    <fieldset>
                        <legend>[{oxmultilang ident="TOXID_GENERAL"}]</legend>
                        <input type="checkbox" name="editval[toxidDontRewriteUrls]" value="1" [{if $toxidDontRewriteUrls}]checked="checked"[{/if}]>
                        [{oxmultilang ident="TOXID_DONT_REWRITE"}]
                    </fieldset>
                </dd>
            </dl>
        </div>
    </div>
    <br />
    <input type="submit" class="edittext" id="oLockButton" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onclick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]>
    <br>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]