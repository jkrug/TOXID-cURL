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

**5 Configure your System in Extensions > TOXID Configuration**

**6 now you can call your snippets via the component like this**

        [{assign var='toxid' value=$oViewConf->getToxid()}]
        [{ $toxid->getCmsSnippet(part1) }]

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
