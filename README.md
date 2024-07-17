# sypets_xclass_di

The following was tested in TYPO3 v11, specifically 11.5.38.

This is an extension which is used to test and analyze problems related
to DI and XCLASS in TYPO3.

It is similar to https://github.com/derhansen/xclass_di from Torben Hansen, but
was created because the solution suggested there did not work for another use case.

## problem

In this extension a hook is used to override the core StandardContentPreviewRenderer.
The class worked if the class was directly used by adding it as preview renderer
in TCA, e.g. via

Configuration/TCA/Overrrides/tt_content.php:

    use Sypets\SypetsXclassDi\Backend\PreviewRenderer\Hook\StandardContentPreviewRenderer as XclassStandardContentPreviewRenderer;
    // ...
    $GLOBALS['TCA']['tt_content']['types']['textmedia']['previewRenderer'] = XclassStandardContentPreviewRenderer::class;

But if the class was added, via XCLASS, the following error was displayed:

    ArgumentCountError
    Too few arguments to function Uniol\Unioltemplate\Backend\PreviewRenderer\Hook\StandardContentPreviewRenderer::__construct(), 0 passed in /var/www/site-uol11/htdocs/typo3/sysext/core/Classes/Utility/GeneralUtility.php on line 3215 and exactly 1 expected

    /var/www/site-uol11/htdocs/typo3/sysext/core/Classes/Utility/GeneralUtility.php line 3215
    return self::$container->get($className);
    }

        // Create new instance and call constructor with parameters
        $instance = new $finalClassName(...$constructorArguments);

## solution / workaround

We added the following lines to make the original core class public in Services.yaml:

    sypets.sypetsxclssdi.xlass_preview:
        class: Sypets\SypetsXclassDi\Backend\PreviewRenderer\Hook\StandardContentPreviewRenderer

    TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer:
        public: true

    typo3.cms.backend.preview.standardcontentpreviewrenderer: '@sypets.sypetsxclssdi.xlass_preview'


If just adding this line, it did not work:

    TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer: '@Sypets\SypetsXclassDi\Backend\PreviewRenderer\Hook\StandardContentPreviewRenderer'

Maybe there is a better way to do this.

## Resources

* docs: https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/ApiOverview/Xclasses/Index.html (see note on bottom of page)
* blog Torben Hansen: https://www.derhansen.de/2021/06/how-to-use-constructor-injection-with-typo3-xclass.html
* extension: https://github.com/derhansen/xclass_di
* issue: https://github.com/derhansen/xclass_di/issues/1
