TOXID cURL
==========

TOXID cURL enables you to load dynamic CMS content into your OXID eShop.
It keeps your userfriendly URLs from the CMS, you could add products as content-type
and they will be loaded also dynamicly from your shop.

Magic!

**So let's start!**

*Please notice:* We are developing on [GitHub](https://github.com/jkrug/TOXID-cURL).
To be sure to get the latest version [go on here](https://github.com/jkrug/TOXID-cURL) or here [go on here](https://github.com/slackero/TOXID-cURL).


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

From version 1.5 on, the minimum OXID version should be 4.6
Thats because we could drop the autoloader and keep nearly all stuff in the module directory.
Also the basic theme is not longer supportet.

If you want to use it for any older version of OXID eShop, checkout the previous version.


Installation & configuration
----------------------------

**1 copy files from the module to /modules/toxid_curl/**

**2 Activate module in admin**

**3 Set up your CMS to deliver the pages in UTF-8 XML-format**

        <?xml version="1.0"?>
        <toxid>
            <part1></part1>
            <part2></part2>
        </toxid>

It is **STRONGLY** recommended to wrap your snippets/parts in CDATA to prevent XML-mistaktes

For Wordpress, change the "Permalinks" settings to use SEO URLs, e.g. by entry name (http://blog.mysite.com/content1) and make sure to adjust the Wordpress .htaccess to allow URL Rewriting.

**5 Configure your System in Extensions > TOXID Configuration**

Specifically, add the URLs to your CMS and enter a URL in "URL Identifier / SEO-Snippet:", e.g. "Blog".
Even if you only use one language, enter a value for the other language, too! Otherwise, the internet might explode, 
or at least you will get the blog contents for every page in your shop. :)

Also, make sure you add a "/" at the end of your CMS URL, e.g. "http://blog.mysite.com/", otherwise you will get double-slashes
in your blog URLs ("../Blog//foo-bar").

**6 now you can call your snippets via the component like this**

        [{assign var='toxid' value=$oViewConf->getToxid()}]
        [{ $toxid->getCmsSnippet('part1') }]

keep in mind, that search keywords will be attached in the end

and in tpl/search.tpl (if you use basic theme), or tpl/page/search/search.tpl add following code:

        [{assign var='toxid' value=$oViewConf->getToxid()}]
        [{assign var='typo3result' value=$toxid->getSearchResult($oView->getSearchParamForHtml())}]
        [{if $typo3result}]
            <div>
                [{$typo3result}]
            </div>
        [{/if}]

**7 inject Oxid \<title\> and \<meta\> keywords and description**

To overwrite Oxid page title **tpl/layout/base.tpl**

        [{assign var="_sMetaTitle" value=$toxid->getCmsMetadata('title')}]

Keywords and Description \<meta\> tag can be overwritten in **tpl/layout/header.tpl**

        [{assign var="description" value=$toxid->getCmsMetadata('description')}]
        [{assign var="keywords" value=$toxid->getCmsMetadata('keywords')}]


**8 Adjust your templates!**


What is implemented already
---------------------------

* done: Multi-Language-Support
* done: Caching
* done: Add products as CMS-Content, rendered by OXID
* demo templates for CMS
* done: Configuration via admin-area
* done: XML caching
* done: seperate function to request title, keywords, description
* …and many more
