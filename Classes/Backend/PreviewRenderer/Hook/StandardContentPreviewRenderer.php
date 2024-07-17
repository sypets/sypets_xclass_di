<?php
declare(strict_types = 1);
namespace Sypets\SypetsXclassDi\Backend\PreviewRenderer\Hook;

use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer as OriginalStandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\SingletonInterface;


/**
 * Im BE im Seitenlayout Zusatzinfos anzeigen
 *
 * Hinweise zur Lösung: Seit TYPO3 v12 wird der hook tt_content_drawFooter entfernt, es ist zwar möglich, ein Event
 * zu nutzen, aber nur für den content (es gibt header, content, footer). Es gibt außerdem die Möglichkeit,
 * einen anderen PreviewHeandler im TCA zu setzen, von einer Klasse zu vererben und den parent aufzurufen,
 * aber es sind diverse previewHandelr im Umlauf.
 *
 * Daher wird hier XCLASS genutzt - das ist nicht so schön, aber kann ggf. später ausgetauscht werden, wenn
 * bessere Lösung existiert. Der hook wird in ext_localconf.php definiert.
 * @see https://stackoverflow.com/questions/78737088/implementing-a-generic-footer-previewrenderer-in-typo3-v12
 *
 * Es wird implements SingletonInterface genutzt, damit die Klasse automatisch erkannt wird und Dependency Injection
 * funktioniert. Es ist jedoch auch möglich, die klasse in Services.yaml als public zu deklarieren.
 *
 * s. Doku:
 * "autoconfigure
 * It is suggested to enable autoconfigure: true as this automatically adds Symfony service tags based on implemented interfaces or base classes. For example, autoconfiguration ensures that classes implementing \TYPO3\CMS\Core\SingletonInterface are publicly available from the Symfony container and marked as shared (shared: true)."
 * @see https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/ApiOverview/DependencyInjection/Index.html#dependency-injection-in-extensions
 */
class StandardContentPreviewRenderer extends OriginalStandardContentPreviewRenderer implements SingletonInterface
{
    public function __construct(private readonly AssetCollector $assetCollector)
    {

    }

    public function renderPageModulePreviewFooter(GridColumnItem $item): string
    {
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

            $info[] = '<strong>Sprachlich verbunden</strong> mit'
                . ' <a class="selectLanguageOriginal" title="Ursprungselement zeigen" style="text-decoration: underline; color: blue !important;" href="javascript:selectOriginalElement('
                . $l18nParent . ')">Element ' . $l18nParent . '</a>'
                . ' der Hauptsprache';

        }
        return $info;
    }

}

