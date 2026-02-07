[{include file="headitem.tpl" title="TOXID cURL Konfiguration"}]
<script type="text/javascript">
<!--
function _groupExp(el) {
    var _cur = el.parentNode;
    if (_cur.className == "exp") _cur.className = "";
    else _cur.className = "exp";
}
//-->
</script>

[{if $readonly}]
[{assign var="readonly" value="readonly disabled"}]
[{else}]
[{assign var="readonly" value=""}]
[{/if}]
<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="toxid_setup_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>
<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="toxid_setup_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="language" value="[{$actlang}]">
    <div class="groupExp">
        <div class="exp">
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>TOXID cURL - CMS Integration</b></a>
            <dl>
                <dd>
                    <p>Integrates CMS content (WordPress, TYPO3 etc.) into OXID eShop via cURL/XML.</p>
                </dd>
            </dl>
        </div>
    </div>
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>Spracheinstellungen</b></a>
            <dl>
                <dd>
                    [{foreach from=$languages key=lang item=olang}]
                        <fieldset>
                            <legend>[{$olang->name}]</legend>
                            <table>
                                <tr>
                                    <td valign="top" class="edittext">CMS Quell-URL:</td>
                                    <td valign="top" class="edittext">
                                        <input type="text" size="80" name="editval[aToxidCurlSource][[{$lang}]]" value="[{$aToxidCurlSource.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">CMS Quell-URL (SSL):</td>
                                    <td valign="top" class="edittext">
                                        <input type="text" size="80" name="editval[aToxidCurlSourceSsl][[{$lang}]]" value="[{$aToxidCurlSourceSsl.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">Such-URL:</td>
                                    <td valign="top" class="edittext">
                                        <input type="text" size="80" name="editval[aToxidSearchUrl][[{$lang}]]" value="[{$aToxidSearchUrl.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">URL-Parameter:</td>
                                    <td valign="top" class="edittext">
                                        <input type="text" size="80" name="editval[aToxidCurlUrlParams][[{$lang}]]" value="[{$aToxidCurlUrlParams.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">URL Identifier / SEO-Snippet:</td>
                                    <td valign="top" class="edittext">
                                        <input type="text" size="80" name="editval[aToxidCurlSeoSnippets][[{$lang}]]" value="[{$aToxidCurlSeoSnippets.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">Preview-Parameter (Admin):</td>
                                    <td valign="top" class="edittext">
                                        <input type="text" size="80" name="editval[aToxidCurlUrlAdminParams][[{$lang}]]" value="[{$aToxidCurlUrlAdminParams.$lang}]">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" class="edittext">404 Fallback-URL:</td>
                                    <td valign="top" class="edittext">
                                        <input type="text" size="80" name="editval[aToxidNotFoundUrl][[{$lang}]]" value="[{$aToxidNotFoundUrl.$lang}]">
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    [{/foreach}]
                    <fieldset>
                        <legend>Allgemein</legend>
                        <table>
                            <tr>
                                <td class="edittext">Erlaubte CMS Request-Parameter:</td>
                                <td class="edittext">
                                    <input type="text" size="80" name="editval[toxidAllowedCmsRequestParams]" value="[{$toxidAllowedCmsRequestParams}]">
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext">Nicht umschreiben (rel-Werte):</td>
                                <td class="edittext">
                                    <input type="text" size="100" name="editval[toxidDontRewriteRelUrls]" value="[{$toxidDontRewriteRelUrls}]">
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext">Nicht umschreiben (Dateiendungen):</td>
                                <td class="edittext">
                                    <input type="text" size="100" name="editval[toxidDontRewriteFileExtension]" value="[{$toxidDontRewriteFileExtension}]">
                                </td>
                            </tr>
                            <tr>
                                <td class="edittext">404 Redirect-Link:</td>
                                <td class="edittext">
                                    <input type="text" size="100" name="editval[toxidError404Link]" value="[{$toxidError404Link}]">
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[toxidRewriteUrlEncoded]" value="0">
                                    <input type="checkbox" name="editval[toxidRewriteUrlEncoded]" value="1" [{if $toxidRewriteUrlEncoded}]checked="checked"[{/if}]>
                                    URL-kodierte Links umschreiben
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[toxidDontRewriteUrls]" value="0">
                                    <input type="checkbox" name="editval[toxidDontRewriteUrls]" value="1" [{if $toxidDontRewriteUrls}]checked="checked"[{/if}]>
                                    URL-Umschreibung deaktivieren
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[bToxidDontPassPostVarsToCms]" value="0">
                                    <input type="checkbox" name="editval[bToxidDontPassPostVarsToCms]" value="1" [{if $bToxidDontPassPostVarsToCms}]checked="checked"[{/if}]>
                                    POST-Variablen nicht an CMS weiterleiten
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[bToxidRedirect301ToStartpage]" value="0">
                                    <input type="checkbox" name="editval[bToxidRedirect301ToStartpage]" value="1" [{if $bToxidRedirect301ToStartpage}]checked="checked"[{/if}]>
                                    301-Redirects zur Startseite
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" class="edittext">
                                    <input type="hidden" name="editval[toxidDontVerifySSLCert]" value="0">
                                    <input type="checkbox" name="editval[toxidDontVerifySSLCert]" value="1" [{if $toxidDontVerifySSLCert}]checked="checked"[{/if}]>
                                    SSL-Zertifikat nicht verifizieren
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>Cache</legend>
                        <table>
                            <tr>
                                <td class="edittext">Cache TTL (Sekunden):</td>
                                <td class="edittext">
                                    <input type="text" size="10" name="editval[toxidCacheTtl]" value="[{$toxidCacheTtl}]">
                                    <span class="edittext">(Standard-TTL, kann pro Snippet im Template ueberschrieben werden)</span>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </dd>
            </dl>
        </div>
    </div>
    <br />
    <input type="submit" class="edittext" id="oLockButton" value="Speichern" onclick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
