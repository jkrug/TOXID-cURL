<?php

namespace Toxid\Core;

use OxidEsales\Eshop\Core\Registry;

/**
 * TOXID cURL - Main engine class
 * Fetches CMS content via cURL, parses XML, rewrites URLs, caches snippets.
 *
 * Ported from OXID 4.x/5.x to OXID 6.5+ with PSR-4 namespaces and PHP 8 compatibility.
 */
class ToxidCurl
{
    /** @var array|null */
    public static $aToxidSnippets = null;

    /** @var string|null */
    public $_sPageTitle = null;

    /** @var string|null */
    public $_sPageDescription = null;

    /** @var string|null */
    public $_sPageKeywords = null;

    /** @var array|null */
    protected $_aSourceUrlByLang = null;

    /** @var array|null */
    protected $_aRewriteStartUrl = null;

    /** @var array|null */
    protected $_aToxidLangUrlParam = null;

    /** @var array|null */
    protected $_aToxidAdminUrlParam = null;

    /** @var array|null */
    protected $_aSearchUrl = null;

    /** @var array */
    protected $_aSearchCache = [];

    /** @var string|null */
    protected $_sCustomPage = null;

    /** @var string|null */
    protected $_sRelValuesForNoRewrite = null;

    /** @var string|null */
    protected $_sFileExtensionValuesForNoRewrite = null;

    /** @var array|null */
    protected $_aNotFoundUrl = null;

    /** @var bool */
    private $_initialized = false;

    /** @var SmartyParser|null */
    private $smartyParser;

    /** @var array|null */
    private $_oSxToxid = null;

    /** @var string|null */
    private $_sPageContent = null;

    /** @var int|null */
    private $iLangId;

    /** @var bool */
    private $cmsAvailable = true;

    /** @var array */
    private $additionalUrlParams = [];

    public function init(SmartyParser $smartyParser): void
    {
        $this->smartyParser = $smartyParser;
        $this->_initialized = true;
    }

    public function getInitialized(): bool
    {
        return $this->_initialized;
    }

    /**
     * Returns the called CMS snippet
     */
    public function getCmsSnippet($snippet = null, $blMultiLang = false, $customPage = null, $iCacheTtl = null, $blGlobalSnippet = false): string
    {
        if (!$this->cmsAvailable) {
            return '';
        }
        if ($snippet === null) {
            return '<strong style="color:red;">TOXID: Please add part, you want to display!</strong>';
        }

        $oConf = $this->getConfig();
        $sShopId = $oConf->getActiveShop()->getId();
        $sLangId = Registry::getLang()->getBaseLanguage();
        $oUtils = Registry::getUtils();
        $oUtilsServer = Registry::get(\OxidEsales\Eshop\Core\UtilsServer::class);

        $sCacheIdent = $this->getCacheIdent($snippet, $sShopId, $sLangId, $blGlobalSnippet);

        // check if snippet text has a ttl and is in cache
        $iCacheTtl = $this->getCacheLifetime($iCacheTtl);
        if ($iCacheTtl !== null && $this->_oSxToxid === null
            && ($sCacheContent = $oUtils->fromFileCache($sCacheIdent))
            && $oUtilsServer->getServerVar('HTTP_CACHE_CONTROL') !== 'no-cache'
        ) {
            // Restore metadata from cache
            if (is_array($sCacheContent)) {
                $this->_sPageTitle = $sCacheContent['title'] ?? null;
                $this->_sPageDescription = $sCacheContent['description'] ?? null;
                $this->_sPageKeywords = $sCacheContent['keywords'] ?? null;
                return $sCacheContent['content'] ?? '';
            }
            return $sCacheContent;
        }

        if ($customPage !== null && $customPage !== '') {
            $this->_sCustomPage = $customPage;
        }

        $sText = $this->_getSnippetFromXml($snippet);
        $sText = $this->_rewriteUrls($sText, null, $blMultiLang);

        $sPageTitle = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//title', null, $blMultiLang));
        $sPageDescription = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//description', null, $blMultiLang));
        $sPageKeywords = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//keywords', null, $blMultiLang));

        $sText = $this->smartyParser->parse($sText);
        $this->_sPageTitle = $this->smartyParser->parse($sPageTitle);
        $this->_sPageDescription = $this->smartyParser->parse($sPageDescription);
        $this->_sPageKeywords = $this->smartyParser->parse($sPageKeywords);

        // SSL URL replacement for images
        if ($oConf->isSsl()) {
            $aSslUrl = $oConf->getShopConfVar('aToxidCurlSourceSsl', $sShopId);
            if (is_array($aSslUrl) && isset($aSslUrl[$sLangId])) {
                $sSslUrl = $aSslUrl[$sLangId];
                $oldSrc = $this->_getToxidLangSource($sLangId);
                $sText = $this->replaceNonSslUrls($sText, $sSslUrl, $oldSrc);
            }
        }

        // save in cache if ttl is set (including metadata for title/description/keywords)
        if ($iCacheTtl !== null) {
            $oUtils->toFileCache($sCacheIdent, [
                'content' => $sText,
                'title' => $this->_sPageTitle,
                'description' => $this->_sPageDescription,
                'keywords' => $this->_sPageKeywords,
            ], $iCacheTtl);
        }

        return $sText;
    }

    /**
     * Returns the TOXID start URL
     */
    public function getToxidStartUrl($iLangId = null): string
    {
        return preg_replace('#index.php\??$#', '', $this->getConfig()->getShopHomeURL()) . $this->_getToxidLangSeoSnippet($iLangId);
    }

    /**
     * Runs search by given keywords in CMS, returns results as HTML
     */
    public function getSearchResult($sKeywords): string
    {
        if (isset($this->_aSearchCache[$sKeywords])) {
            return $this->_aSearchCache[$sKeywords];
        }
        $this->_aSearchCache[$sKeywords] = '';
        $sSearchStartUrl = $this->_getToxidSearchUrl();

        if (!$sSearchStartUrl) {
            return '';
        }

        $aSearchResults = $this->_getRemoteContent($sSearchStartUrl . $sKeywords);

        if (isset($aSearchResults['info']['http_code']) && $aSearchResults['info']['http_code'] == 200) {
            $sSearchResult = $aSearchResults['content'];
            $sSearchResult = $this->_rewriteUrls($sSearchResult);
            $this->_aSearchCache[$sKeywords] = $sSearchResult;
        }

        return $this->_aSearchCache[$sKeywords];
    }

    /**
     * Add additional URL parameter to CMS request
     */
    public function setAdditionalUrlParam(string $key, string $value): void
    {
        $this->additionalUrlParams[$key] = $value;
    }

    /**
     * Returns SimpleXMLElement object from CMS XML
     */
    protected function _getXmlObject($blReset = false)
    {
        $customPage = $this->_getToxidCustomPage();

        if (
            isset($this->_oSxToxid[$customPage]) &&
            ($this->_oSxToxid[$customPage] instanceof \SimpleXMLElement) &&
            !$blReset
        ) {
            return $this->_oSxToxid[$customPage];
        }

        $xml = $this->_getXmlFromCms($blReset);

        if (!$this->isXml($xml)) {
            return new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<toxid/>");
        }

        $this->_oSxToxid[$customPage] = new \SimpleXMLElement($xml);

        return $this->_oSxToxid[$customPage];
    }

    /**
     * Determines if we got XML from CMS or not
     */
    protected function isXml($response): bool
    {
        return is_string($response) && str_starts_with($response, '<?xml ');
    }

    /**
     * Returns snippet text from XML object
     */
    protected function _getSnippetFromXml($sSnippet): string
    {
        $oXml = $this->_getXmlObject();
        $aXpathSnippets = $oXml->xpath('//' . $sSnippet . '[1]');

        if (!is_array($aXpathSnippets) || empty($aXpathSnippets)) {
            return '';
        }

        return (string) $aXpathSnippets[0];
    }

    /**
     * Returns raw string from CMS page
     */
    protected function _getXmlFromCms($blReset = false): string
    {
        if ($this->_sPageContent !== null && !$blReset) {
            return $this->_sPageContent;
        }

        $sUrl = $this->buildBaseUrl();
        $aPage = $this->getRemoteContentAndHandleStatusCodes($sUrl);

        // Kill everything before the <?xml
        $this->_sPageContent = preg_replace('/.*<\?xml/ms', '<?xml', $aPage['content'] ?? '');

        // Remove non-utf8 characters
        $regex = '/((?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})|./x';
        $this->_sPageContent = preg_replace($regex, '$1', $this->_sPageContent);

        return $this->_sPageContent;
    }

    /**
     * Fetches remote content via cURL
     */
    protected function _getRemoteContent($sUrl): array
    {
        $aResult = [];
        $curl_handle = curl_init();

        $sUrl = $this->buildRequestUrl($sUrl);

        curl_setopt($curl_handle, CURLOPT_URL, $sUrl);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);

        if ($this->getConfig()->getConfigParam('toxidDontVerifySSLCert')) {
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
        }

        // Forward POST requests
        if (!empty($_POST) && !$this->getConfig()->getConfigParam('bToxidDontPassPostVarsToCms')) {
            $postRequest = http_build_query($_POST, '', '&');
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postRequest);
        }

        $aResult['content'] = curl_exec($curl_handle);
        $aResult['info'] = curl_getinfo($curl_handle);
        curl_close($curl_handle);

        return $aResult;
    }

    /**
     * Rewrites CMS URLs to OXID SEO URLs
     */
    protected function _rewriteUrls($sContent, $iLangId = null, $blMultiLang = false): string
    {
        if (!is_string($sContent) || $sContent === '') {
            return '';
        }

        if ($this->getConfig()->getConfigParam('toxidDontRewriteUrls')) {
            return $sContent;
        }

        if ($blMultiLang == false) {
            if ($iLangId === null) {
                $iLangId = Registry::getLang()->getBaseLanguage();
            }
            $aLanguages = [$iLangId];
        } else {
            $aLangs = $this->getConfig()->getConfigParam('aToxidCurlSource');
            if (!is_array($aLangs)) {
                return $sContent;
            }
            arsort($aLangs);
            $aLanguages = array_keys($aLangs);
        }

        foreach ($aLanguages as $iLangId) {
            $sShopUrl = $this->getConfig()->getShopUrl();

            if (substr($sShopUrl, -1) !== '/') {
                $sShopUrl .= '/';
            }
            $target = rtrim($sShopUrl . $this->_getToxidLangSeoSnippet($iLangId), '/') . '/';
            $source = $this->_getToxidLangSource($iLangId);

            if (!$source) {
                continue;
            }

            // Collect all source URLs to rewrite (main + SSL as alternative)
            $aSources = [$source];
            $aSslUrls = $this->getConfig()->getConfigParam('aToxidCurlSourceSsl');
            if (is_array($aSslUrls) && !empty($aSslUrls[$iLangId])) {
                $sSslSource = rtrim($aSslUrls[$iLangId], '/') . '/';
                if ($sSslSource !== $source) {
                    $aSources[] = $sSslSource;
                }
            }

            foreach ($aSources as $currentSource) {
                $pattern = '%(action|href|src|srcset)=[\'"]' . preg_quote($currentSource, '%') . '[^"\']*?(?:/|\.html|\.php|\.asp)?(?:\?[^"\']*)?[\'"]%';

                preg_match_all($pattern, $sContent, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {

                    // skip rewrite for defined file extensions
                    $fileExtExclude = $this->_getFileExtensionValuesForNoRewrite();
                    if ($fileExtExclude) {
                        if (preg_match('%\.(' . $fileExtExclude . ')[\'"]*%i', $match[0])) {
                            continue;
                        }
                    }

                    // skip rewrite for defined rel values
                    $relExclude = $this->_getRelValuesForNoRewrite();
                    if ($relExclude) {
                        if (preg_match('%rel=["\'](' . $relExclude . ')["\']%', $match[0])) {
                            continue;
                        }
                    }

                    $rewritten = str_replace($currentSource, $target, $match[0]);

                    // Ensure trailing slash for href/action URLs (not for src/srcset)
                    if ($match[1] === 'href' || $match[1] === 'action') {
                        if (preg_match('#[\'"]([^\'"]+)[\'"]#', $rewritten, $urlMatch)) {
                            $url = $urlMatch[1];
                            $pathEnd = strcspn($url, '?#');
                            $path = substr($url, 0, $pathEnd);
                            if ($path !== '' && substr($path, -1) !== '/' && !preg_match('#\.\w{2,5}$#', $path)) {
                                $rewritten = str_replace($url, $path . '/' . substr($url, $pathEnd), $rewritten);
                            }
                        }
                    }

                    $sContent = str_replace($match[0], $rewritten, $sContent);
                }
            }
        }

        return $sContent;
    }

    /**
     * Returns language-specific source URL
     */
    protected function _getToxidLangSource($iLangId = null, $blReset = false): string
    {
        if ($this->_aSourceUrlByLang === null || $blReset) {
            $this->_aSourceUrlByLang = $this->getConfig()->getConfigParam('aToxidCurlSource');
        }
        if ($iLangId === null) {
            $iLangId = Registry::getLang()->getBaseLanguage();
        }

        $source = is_array($this->_aSourceUrlByLang) && isset($this->_aSourceUrlByLang[$iLangId])
            ? $this->_aSourceUrlByLang[$iLangId]
            : '';

        if ($source !== '' && substr($source, -1) !== '/') {
            return $source . '/';
        }

        return $source;
    }

    /**
     * Returns language-specific URL parameters
     */
    protected function _getToxidLangUrlParam($iLangId = null, $blReset = false): string
    {
        if ($this->_aToxidLangUrlParam === null || $blReset) {
            $this->_aToxidLangUrlParam = $this->getConfig()->getConfigParam('aToxidCurlUrlParams');
        }
        if ($iLangId === null) {
            $iLangId = Registry::getLang()->getBaseLanguage();
        }

        $param = is_array($this->_aToxidLangUrlParam) && isset($this->_aToxidLangUrlParam[$iLangId])
            ? $this->_aToxidLangUrlParam[$iLangId]
            : '';

        return '?' . ltrim($param, '?');
    }

    /**
     * Returns all TOXID URL parameters
     */
    protected function _getToxidUrlParams(): string
    {
        $langParam = $this->_getToxidLangUrlParam();
        $params = $langParam;

        if ($this->isAdminLoggedIn()) {
            $params .= $this->_getToxidAdminUrlParam();
            $params .= $this->_getToxidCmsUrlParams();
        }

        return $params;
    }

    /**
     * Returns admin-specific URL parameters
     */
    protected function _getToxidAdminUrlParam(): string
    {
        $this->_aToxidAdminUrlParam = $this->getConfig()->getConfigParam('aToxidCurlUrlAdminParams');

        if (!is_array($this->_aToxidAdminUrlParam) || empty($this->_aToxidAdminUrlParam)) {
            return '';
        }

        return '&' . ltrim($this->_aToxidAdminUrlParam[0] ?? '', '?');
    }

    /**
     * Returns CMS-specific URL parameters from request
     */
    protected function _getToxidCmsUrlParams(): string
    {
        $allowedParams = $this->getConfig()->getConfigParam('toxidAllowedCmsRequestParams');
        if (!$allowedParams) {
            return '';
        }

        $params = explode(",", $allowedParams);
        $cmsParams = '';

        foreach ($params as $param) {
            $param = trim($param);
            $paramValue = Registry::getConfig()->getRequestParameter($param);

            if ($paramValue !== null && $paramValue !== '') {
                $cmsParams .= '&' . $param . '=' . $paramValue;
            }
        }

        return $cmsParams;
    }

    /**
     * Returns language-specific SEO snippet
     */
    protected function _getToxidLangSeoSnippet($iLangId = null, $blReset = false): string
    {
        if ($this->_aRewriteStartUrl === null || $blReset) {
            $this->_aRewriteStartUrl = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');
        }
        if ($iLangId === null) {
            $iLangId = Registry::getLang()->getBaseLanguage();
        }

        return is_array($this->_aRewriteStartUrl) && isset($this->_aRewriteStartUrl[$iLangId])
            ? $this->_aRewriteStartUrl[$iLangId]
            : '';
    }

    /**
     * Returns the currently defined custom URL
     */
    protected function _getToxidCustomPage(): string
    {
        return ($this->_sCustomPage !== null) ? $this->_sCustomPage : '';
    }

    /**
     * Returns CMS search URL
     */
    protected function _getToxidSearchUrl($iLangId = null, $blReset = false): string
    {
        if ($this->_aSearchUrl === null || $blReset) {
            $this->_aSearchUrl = $this->getConfig()->getConfigParam('aToxidSearchUrl');
        }
        if ($iLangId === null) {
            $iLangId = Registry::getLang()->getBaseLanguage();
        }

        return is_array($this->_aSearchUrl) && isset($this->_aSearchUrl[$iLangId])
            ? $this->_aSearchUrl[$iLangId]
            : '';
    }

    /**
     * Returns rel values for which URLs should not be rewritten
     */
    protected function _getRelValuesForNoRewrite(): string
    {
        if ($this->_sRelValuesForNoRewrite === null) {
            $val = $this->getConfig()->getConfigParam('toxidDontRewriteRelUrls');
            $this->_sRelValuesForNoRewrite = $val ? implode('|', explode(',', str_replace(' ', '', $val))) : '';
        }

        return $this->_sRelValuesForNoRewrite;
    }

    /**
     * Returns file extensions for which URLs should not be rewritten
     */
    protected function _getFileExtensionValuesForNoRewrite(): string
    {
        if ($this->_sFileExtensionValuesForNoRewrite === null) {
            $val = $this->getConfig()->getConfigParam('toxidDontRewriteFileExtension');
            $this->_sFileExtensionValuesForNoRewrite = $val ? implode('|', explode(',', str_replace(' ', '', $val))) : '';
        }

        return $this->_sFileExtensionValuesForNoRewrite;
    }

    /**
     * Returns not-found URL for the CMS
     */
    protected function _getToxidNotFoundUrl($iLangId = null, $blReset = false): ?string
    {
        if ($this->_aNotFoundUrl === null || $blReset) {
            $this->_aNotFoundUrl = $this->getConfig()->getConfigParam('aToxidNotFoundUrl');
        }
        if ($iLangId === null) {
            $iLangId = Registry::getLang()->getBaseLanguage();
        }

        return is_array($this->_aNotFoundUrl) && array_key_exists($iLangId, $this->_aNotFoundUrl)
            ? $this->_aNotFoundUrl[$iLangId]
            : null;
    }

    /**
     * Handles HTTP status codes for TOXID response
     */
    protected function getRemoteContentAndHandleStatusCodes(string $sUrl, bool $notFound404 = false): array
    {
        $aPage = $this->_getRemoteContent($sUrl);
        $httpCode = $aPage['info']['http_code'] ?? 0;

        switch ($httpCode) {
            case 500:
                header("HTTP/1.1 500 Internal Server Error");
                header('Location: ' . $this->getConfig()->getShopHomeURL());
                Registry::getUtils()->showMessageAndExit('');
                break;
            case 404:
                if ($this->_getToxidNotFoundUrl() && !$notFound404) {
                    header("HTTP/1.0 404 Not Found");
                    $aPage = $this->getRemoteContentAndHandleStatusCodes($this->_getToxidNotFoundUrl(), true);
                    break;
                }
                $this->handleError(404, $aPage['info']['url'] ?? '');
                break;
            case 301:
                if ($this->getConfig()->getConfigParam('bToxidRedirect301ToStartpage')) {
                    header("HTTP/1.1 301 Moved Permanently");
                    header('Location: ' . $this->getToxidStartUrl());
                    Registry::getUtils()->showMessageAndExit('');
                } else {
                    $redirectUrl = $this->prepareRedirectUrl($aPage['info']['redirect_url'] ?? '');
                    Registry::getUtils()->redirect($redirectUrl, false, 301);
                }
                break;
            case 302:
            case 303:
            case 307:
                $redirectUrl = $aPage['info']['redirect_url'] ?? '';
                if ($redirectUrl) {
                    $aPage = $this->getRemoteContentAndHandleStatusCodes($redirectUrl);
                }
                break;
            case 0:
                header('Location: ' . $this->getConfig()->getShopHomeURL());
                Registry::getUtils()->showMessageAndExit('');
                break;
        }

        return $aPage;
    }

    /**
     * Prepares URL for redirect in shop
     */
    private function prepareRedirectUrl(string $sUrl): string
    {
        $iLangId = $this->getBaseLanguage();
        $sShopUrl = $this->getConfig()->getShopUrl();
        if (substr($sShopUrl, -1) !== '/') {
            $sShopUrl .= '/';
        }
        $target = rtrim($sShopUrl . $this->_getToxidLangSeoSnippet($iLangId), '/') . '/';

        // Try main source URL
        $source = $this->_getToxidLangSource($iLangId);
        if ($source && strpos($sUrl, $source) !== false) {
            return str_replace($source, $target, $sUrl);
        }

        // Try SSL/alternative source URL
        $aSslUrls = $this->getConfig()->getConfigParam('aToxidCurlSourceSsl');
        if (is_array($aSslUrls) && !empty($aSslUrls[$iLangId])) {
            $sSslSource = rtrim($aSslUrls[$iLangId], '/') . '/';
            if (strpos($sUrl, $sSslSource) !== false) {
                return str_replace($sSslSource, $target, $sUrl);
            }
        }

        return $sUrl;
    }

    /**
     * Getter for base language
     */
    private function getBaseLanguage(): int
    {
        if ($this->iLangId === null) {
            $this->iLangId = Registry::getLang()->getBaseLanguage();
        }

        return $this->iLangId;
    }

    /**
     * Replaces non-SSL URLs with SSL URLs for images
     */
    private function replaceNonSslUrls(string $sText, string $sSslUrl, string $oldSrc): string
    {
        if (!empty($sSslUrl) && $oldSrc !== '' && $sSslUrl !== '') {
            $sText = str_replace('src="' . $oldSrc, 'src="' . $sSslUrl, $sText);
        }

        return $sText;
    }

    /**
     * Getter for oxConfig
     */
    private function getConfig()
    {
        return Registry::getConfig();
    }

    /**
     * Handle TOXID request errors
     */
    protected function handleError(int $statusCode, string $sUrl = ''): void
    {
        $this->cmsAvailable = false;

        switch ($statusCode) {
            case 404:
                $sRedirectLinkError404 = $this->getConfig()->getConfigParam('toxidError404Link');
                if ($sRedirectLinkError404 && trim($sRedirectLinkError404) !== '') {
                    Registry::getUtils()->redirect($sRedirectLinkError404);
                }
                Registry::getUtils()->handlePageNotFoundError($sUrl);
                break;
        }
    }

    private function getCacheLifetime($iCacheTtl)
    {
        if (null === $iCacheTtl) {
            $defaultCacheTtl = $this->getConfig()->getConfigParam('toxidCacheTtl');
            if ($defaultCacheTtl !== null && trim($defaultCacheTtl) !== '') {
                $iCacheTtl = (int) $defaultCacheTtl;
            }
        }

        return $iCacheTtl;
    }

    private function getCacheIdent(string $snippet, string $sShopId, $sLangId, bool $blGlobalSnippet): string
    {
        $identString = $snippet . $this->buildRequestUrl($this->buildBaseUrl());

        if (!$blGlobalSnippet) {
            $identString .= $this->getConfig()->getConfigParam('sToxidCurlPage');
        }

        $identHash = md5($identString);

        return "toxid_snippet_{$identHash}_{$sShopId}_{$sLangId}";
    }

    /**
     * Check if admin user is logged in
     */
    private function isAdminLoggedIn(): bool
    {
        $user = Registry::getConfig()->getUser();

        return ($user && isset($user->oxuser__oxrights) && $user->oxuser__oxrights->value !== 'user');
    }

    /**
     * Builds the full request URL with additional parameters
     */
    protected function buildRequestUrl(string $baseUrl): string
    {
        $params = http_build_query($this->additionalUrlParams);
        if (strpos($baseUrl, '?') === false) {
            $baseUrl .= "?{$params}";
        } else {
            $baseUrl = rtrim($baseUrl, '&') . "&{$params}";
        }

        $baseUrl = rtrim($baseUrl, '&?');

        return $baseUrl;
    }

    /**
     * Builds the base CMS URL
     */
    protected function buildBaseUrl(): string
    {
        $source = $this->_getToxidLangSource();
        $page = $this->getConfig()->getConfigParam('sToxidCurlPage') ?? '';
        $custom = $this->_getToxidCustomPage();
        $params = $this->_getToxidUrlParams();

        return $source . $custom . $page . $params;
    }
}
