<?php
declare(strict_types=1);

use Sypets\SypetsXclassDi\Backend\PreviewRenderer\Hook\StandardContentPreviewRenderer as XclassStandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer as CoreStandardContentPreviewRenderer;

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][CoreStandardContentPreviewRenderer::class] = [
    'className' => XclassStandardContentPreviewRenderer::class
];


