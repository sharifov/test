<?php

use dosamigos\ckeditor\CKEditorAsset;
use frontend\assets\overridden\ImperaviAsset;
use frontend\assets\overridden\KartikActiveFormAsset;
use frontend\assets\overridden\KartikDialogBootstrapAsset;
use frontend\assets\overridden\KartikEditableAsset;
use frontend\assets\overridden\KartikEditablePjaxAsset;
use frontend\assets\overridden\KartikExportMenuAsset;
use frontend\assets\overridden\KartikGridExportAsset;
use frontend\assets\overridden\KartikGridResizeColumnsAsset;
use frontend\assets\overridden\KartikGridViewAsset;
use frontend\assets\overridden\KDNJsonEditorAsset;
use kartik\daterange\MomentAsset;
use kartik\dialog\DialogBootstrapAsset;
use kartik\editable\EditableAsset;
use kartik\editable\EditablePjaxAsset;
use kartik\export\ExportMenuAsset;
use kartik\form\ActiveFormAsset;
use kartik\grid\GridExportAsset;
use kartik\grid\GridResizeColumnsAsset;
use kartik\grid\GridViewAsset;
use kdn\yii2\assets\JsonEditorFullAsset;
use kdn\yii2\assets\JsonEditorMinimalistAsset;
use vova07\imperavi\Asset;

$assetsProd = require __DIR__ . '/assets-prod.php';

return array_merge($assetsProd, [
    CKEditorAsset::class => [
        'sourcePath' => null,
        'js' => [
            'https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js',
        ],
        'depends' => []
    ],
    EditableAsset::class => [
        'class' => KartikEditableAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikEditableAsset::class,
        ]
    ],
    ExportMenuAsset::class => [
        'class' => KartikExportMenuAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikExportMenuAsset::class,
        ]
    ],
    DialogBootstrapAsset::class => [
        'class' => KartikDialogBootstrapAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikDialogBootstrapAsset::class,
        ]
    ],
    ActiveFormAsset::class => [
        'class' => KartikActiveFormAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikActiveFormAsset::class,
        ]
    ],
    Asset::class => [
        'class' => ImperaviAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            ImperaviAsset::class,
        ]
    ],
    EditablePjaxAsset::class => [
        'class' => KartikEditablePjaxAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikEditablePjaxAsset::class,
        ]
    ],
    JsonEditorFullAsset::class => [
        'class' => KDNJsonEditorAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KDNJsonEditorAsset::class,
        ]
    ],
    JsonEditorMinimalistAsset::class => [
        'class' => KDNJsonEditorAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KDNJsonEditorAsset::class,
        ]
    ],
    GridViewAsset::class => [
        'class' => KartikGridViewAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikGridViewAsset::class,
        ]
    ],
    GridResizeColumnsAsset::class => [
        'class' => KartikGridResizeColumnsAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikGridResizeColumnsAsset::class,
        ]
    ],
    GridExportAsset::class => [
        'class' => KartikGridExportAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikGridExportAsset::class,
        ]
    ],
    MomentAsset::class => [
        'class' => \frontend\assets\MomentAsset::class
    ]
]);
