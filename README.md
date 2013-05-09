TOXID cURL
==========
TOXID cURL enables you to load dynamic CMS content into your OXID eShop.
It keeps your userfriendly URLs from the CMS, you could add products as content-type
and they will be loaded also dynamicly from your shop.

Magic!

So let's start!


Please notice: We are developing on GitHub.
To be sure to get the latest version, look there
    https://github.com/jkrug/TOXID-cURL


What it is
----------
*    Renders CMS-pages and Navigation in OXID from any XML-Source

*    adjusts link-urls from source


What it is NOT
--------------
*    NO Single-Sign-On (so no restriced pages are possible)
     See TOXID with OxAPI

*    NO out-of-the-box solution


System requirements
-------------------
From version 1.5 on, the minumum OXID version should be 4.6
Thats because we could drop the autoloader and keep nearly all stuff in the module directory.
Also the basic theme is not longer supportet.

If you want to use it for any older version of OXID eShop, checkout the previous version.

Installation & configuration
----------------------------
**1 copy files from the OXID directory to your shop root**

**2 Activate module in admin**

**3 Set up your CMS to deliver the pages in UTF-8 XML-format**

        <?xml version="1.0"?>
        <toxid>
            <part1></part1>
            <part2></part2>
        </toxid>

It is STRONGLY recommended to wrap your snippets/parts in CDATA to prevent XML-mistaktes

**5 Configure your System in config.inc.php**

        // your URL-Snippet to dectect TOXID-Pages
        $this->aToxidCurlSeoSnippets = array(
            '0' => 'toxid-curl',
            '1' => 'toxid-curl-en',
        );
        // URL to your CMS
        $this->aToxidCurlSource	= array(
            '0' => 'http://yourcmspage.com/',
            '1' => 'http://yourcmspage.com/en/',
        );
        // Additional Params for URL
        $this->aToxidCurlUrlParams = array(
            '0' => '?type=1',
            '1' => '?type=1',
        );
        // If actual Site is an SSL-site, replace first URL with second URL (only replaces image-source-attributes)
        $this->aToxidCurlSslPicReplacement = array(
            '0' => array('http://yourcmspage.com/', 'https://yourcmspage.com'),
            '1' => array('http://yourcmspage.com/en/', 'https://yourcmspage.com/en/'),
        );
        //If you don't want to rewrite your URLs
        $this->toxidDontRewriteUrls= true;

**6 now you can call your snippets via the component like this**

        [{assign var='toxid' value=$oViewConf->getToxid()}]
        [{ $toxid->getCmsSnippet(part1) }]

**7 to use search functionality, config.inc.php mus contain search urls:**

        // typo3 search url
        $this->aToxidSearchUrl	= array(
            '0' => 'http://www.marmalade.de/nc/suche.html?tx_indexedsearch[sword]=',
            '1' => 'http://www.marmalade.de/nc/suche.html?tx_indexedsearch[sword]=',
        );

keep in mind, that search keywords will be attached in the end

and in tpl/search.tpl (if you use basic theme), or tpl/page/search/search.tpl add following code:

        [{assign var='toxid' value=$oViewConf->getToxid()}]
        [{assign var='typo3result' value=$toxid->getSearchResult($oView->getSearchParamForHtml())}]
        [{if $typo3result}]
            <div>
                [{$typo3result}]
            </div>
        [{/if}]

**8 Adjust your templates!**


What we already implemented
----------------------------
*    done: Multi-Language-Support

*    done: Caching

*    done: Add products as CMS-Content, rendered by OXID

*    demo templates for CMS

*    done: Configuration via admin-area
