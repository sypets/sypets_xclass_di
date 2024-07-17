<?php
declare(strict_types = 1);
namespace Sypets\SypetsXclassDi\Backend\PreviewRenderer\Hook;

use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer as OriginalStandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\SingletonInterface;


/**
 * Show additional information in footer for PageLayout.
 *
 * Note: Since TYPO3 v12 the hook tt_content_drawFooter is removed, it is now possible to use an event,
 * but this only allows to modify the content, not the footer. It is also possible to set the preview
 * handler in TCA, however the functionality here should apply to all preview renderers and there are
 * several preview renderers used (and we need to inherit here and only override the footer).
 *
 * For this reason XCLASS is used:
 * @see https://stackoverflow.com/questions/78737088/implementing-a-generic-footer-previewrenderer-in-typo3-v12
 */
// if not using implements SingletonInterface might have to configure the class public in Services.yaml
class StandardContentPreviewRenderer extends OriginalStandardContentPreviewRenderer //implements SingletonInterface
{
    public function __construct(private readonly AssetCollector $assetCollector)
    {

    }

    public function renderPageModulePreviewFooter(GridColumnItem $item): string
    {
        $content = '';
        $record = $item->getRecord();
        $ctype = $record['CType'] ?? '';
        $infos = $this->setFooterInfo($ctype, $record);
        $infos[] = sprintf('sypets hook: uid=%d l18n_parent=%d', (int)$record['uid'], (int)$record['l18n_parent']);
        foreach ($infos as $info) {
            $content .= '<p>' . $info . '</p>';
        }
        return $content . parent::renderPageModulePreviewFooter($item);
    }

    protected function addAssetsForLanguageSelectOriginalElement(): void
    {
        $this->assetCollector->addJavaScript(
            'sypets_xclass_di.js',
            'EXT:sypets_xclass_di/Resources/Public/Backend/JavaScript/LanguageSelectOriginalElement.js'
        );

    }

    protected function setFooterInfo(string $ctype, array $row): array
    {
        $info = [];

        // todo ... code removed

        $l18nParent = (int)($row['l18n_parent'] ?? 0);
        if ($l18nParent > 0) {
            $this->addAssetsForLanguageSelectOriginalElement();

            $info[] = '<strong>Connected to </strong>'
                . ' <a class="selectLanguageOriginal" title="Mark element of original language" style="text-decoration: underline; color: blue !important;" href="javascript:selectOriginalElement('
                . $l18nParent . ')">Element ' . $l18nParent . '</a>'
                . ' of original language';

        }
        return $info;
    }

}

