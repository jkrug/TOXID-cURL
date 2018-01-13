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
                                <a href="http://www.marmalade.de/" target="_blank" style="float:left;margin:0 20px 10px 0">
                                    <img src="../modules/toxid_curl/toxid.jpg" width="90px" />
                                </a>
                                [{oxmultilang ident='TOXID_SUPPORT_DESC'}]
                            </td>
                            <td valign="top" style="padding-right:30px">
                                <b>[{oxmultilang ident='TOXID_INTEGRATIONPARTNER'}]</b>
                                <ul>
                                    <li><a href="http://www.marmalade.de/" target="_blank">marmalade GmbH</a></li>
                                    <li><a href="https://www.makaira.io/" target="_blank">Makaira - E-Commerce Marketing Suite</a></li>
                                    <li><a href="https://scale.sc/" target="_blank">ScaleCommerce GmbH</a></li>
                                    <li><a href="http://www.gn2-netwerk.de/" target="_blank">GN2 Netwerk</a></li>
                                    <li><a href="https://www.kuehlhaus.com/" target="_blank">kuehlhaus AG</a></li>
                                </ul>
                            </td>
                            <td valign="top" style="padding-right:30px">
                                <b>Contributors</b>
                                <ul>
                                    <li><a href="https://github.com/dajoho" target="_blank">Dave Holloway</a></li>
                                    <li><a href="https://github.com/vanilla-thunder" target="_blank">Marat</a></li>
                                    <li><a href="https://github.com/Tr0nYx" target="_blank">Tr0nYx</a></li>
                                    <li><a href="https://github.com/pixelkrieg" target="_blank">pixelkrieg</a></li>
                                    <li><a href="https://github.com/wanis" target="_blank">Mažvydas Skuodas</a></li>
                                    <li><a href="https://github.com/adriankirchner" target="_blank">Adrian Kirchner</a></li>
                                </ul>
                            </td>
                            <td valign="top">
                                <b>&nbsp;</b>
                                <ul>
                                    <li><a href="https://github.com/jkrug" target="_blank">Joscha Krug</a></li>
                                    <li><a href="https://github.com/elflein" target="_blank">Jan Elflein</a></li>
                                    <li><a href="https://github.com/danielliebig" target="_blank">Daniel Liebig</a></li>
                                    <li><a href="https://github.com/web-xpression" target="_blank">Dirk Kühne</a></li>
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
                                <tr>
                                    <td valign="top" class="edittext">
                                        [{oxmultilang ident="TOXID_PREVIEW_PARAM"}]:
                                    </td>
                                    <td valign="top" class="edittext">
                                        <input type="text" name="editval[aToxidCurlUrlAdminParams][[{ $lang }]]" value="[{$aToxidCurlUrlAdminParams.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">
                                        [{oxmultilang ident="TOXID_NOT_FOUND_URL"}]:
                                    </td>
                                    <td valign="top" class="edittext">
                                        <input type="text" name="editval[aToxidNotFoundUrl][[{ $lang }]]" value="[{$aToxidNotFoundUrl.$lang}]">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    [{/foreach}]
                    <fieldset>
                        <legend>[{oxmultilang ident="TOXID_GENERAL"}]</legend>
                        <table>
                            <tr>
                                <td class="edittext">
                                    [{ oxmultilang ident="TOXID_CMS_PARAMS" }]
                                </td>
                                <td class="edittext">
                                    <input type="text"  name="editval[toxidAllowedCmsRequestParams]" value="[{$toxidAllowedCmsRequestParams}]">
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext">
                                    [{oxmultilang ident="TOXID_DONT_REWRITE_REL_URLS"}]:
                                </td>
                                <td class="edittext">
                                    <input type="text" name="editval[toxidDontRewriteRelUrls]" value="[{$toxidDontRewriteRelUrls}]" size="100">
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext">
                                    [{oxmultilang ident="TOXID_DONT_REWRITE_URLS_WITH_FILE_EXTENSIONS"}]:
                                </td>
                                <td class="edittext">
                                    <input type="text" name="editval[toxidDontRewriteFileExtension]" value="[{$toxidDontRewriteFileExtension}]" size="100">
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext">
                                    [{ oxmultilang ident="TOXID_ERROR_404_LINK" }]:
                                </td>
                                <td class="edittext">
                                    <input type="text" name="editval[toxidError404Link]" value="[{$toxidError404Link}]" size="100">
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[toxidRewriteUrlEncoded]" value="0">
                                    <input type="checkbox" name="editval[toxidRewriteUrlEncoded]" value="1" [{if $toxidRewriteUrlEncoded}]checked="checked"[{/if}]>
                                    [{oxmultilang ident="TOXID_REWRITE_URLENCODED"}]
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[toxidDontRewriteUrls]" value="0">
                                    <input type="checkbox" name="editval[toxidDontRewriteUrls]" value="1" [{if $toxidDontRewriteUrls}]checked="checked"[{/if}]>
                                    [{oxmultilang ident="TOXID_DONT_REWRITE"}]
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[bToxidDontPassPostVarsToCms]" value="0">
                                    <input type="checkbox" name="editval[bToxidDontPassPostVarsToCms]" value="1" [{if $bToxidDontPassPostVarsToCms}]checked="checked"[{/if}]>
                                    [{oxmultilang ident="TOXID_DONT_PASSTHROUGH"}]
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[bToxidRedirect301ToStartpage]" value="0">
                                    <input type="checkbox" name="editval[bToxidRedirect301ToStartpage]" value="1" [{if $bToxidRedirect301ToStartpage}]checked="checked"[{/if}]>
                                    [{oxmultilang ident="TOXID_REDIRECT_301_TO_STARTPAGE"}]
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[toxidDontVerifySSLCert]" value="0">
                                    <input type="checkbox" name="editval[toxidDontVerifySSLCert]" value="1" [{if $bToxidRedirect301ToStartpage}]checked="checked"[{/if}]>
                                    [{oxmultilang ident="TOXID_DONT_VERIFY_SSL_CERTIFICATE"}]
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>[{oxmultilang ident="TOXID_CACHE"}]</legend>
                        <table>
                            <tr>
                                <td class="edittext">
                                    [{oxmultilang ident="TOXID_CACHE_TTL"}]:
                                </td>
                                <td class="edittext">
                                    <input type="text" name="editval[toxidCacheTtl]" value="[{$toxidCacheTtl}]" size="10">[{oxinputhelp ident="HELP_TOXID_CACHE_TTL"}]
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </dd>
            </dl>
        </div>
    </div>
    <br />
    <input type="submit" class="edittext" id="oLockButton" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onclick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]>
    <br>
    <br>
    <br>
    <br>
    <br>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
