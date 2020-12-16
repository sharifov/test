<?php

use dosamigos\ckeditor\CKEditorAsset;
use frontend\assets\groups\BootstrapGroupAsset;
use frontend\assets\overridden\ImperaviAsset;
use frontend\assets\overridden\KartikActiveFormAsset;
use frontend\assets\overridden\KartikCheckboxColumnAsset;
use frontend\assets\overridden\KartikDialogBootstrapAsset;
use frontend\assets\overridden\KartikEditableAsset;
use frontend\assets\overridden\KartikEditablePjaxAsset;
use frontend\assets\overridden\KartikExportMenuAsset;
use frontend\assets\overridden\KartikGridExportAsset;
use frontend\assets\overridden\KartikGridFloatHeadAsset;
use frontend\assets\overridden\KartikGridResizeColumnsAsset;
use frontend\assets\overridden\KartikGridToggleDataAsset;
use frontend\assets\overridden\KartikGridViewAsset;
use frontend\assets\overridden\KDNJsonEditorAsset;
use frontend\assets\overridden\LajaxLanguageItemPluginAsset;
use kartik\daterange\MomentAsset;
use kartik\dialog\DialogBootstrapAsset;
use kartik\editable\EditableAsset;
use kartik\editable\EditablePjaxAsset;
use kartik\export\ExportMenuAsset;
use kartik\form\ActiveFormAsset;
use kartik\grid\CheckboxColumnAsset;
use kartik\grid\GridExportAsset;
use kartik\grid\GridFloatHeadAsset;
use kartik\grid\GridResizeColumnsAsset;
use kartik\grid\GridToggleDataAsset;
use kartik\grid\GridViewAsset;
use kdn\yii2\assets\JsonEditorFullAsset;
use kdn\yii2\assets\JsonEditorMinimalistAsset;
use lajax\translatemanager\bundles\LanguageItemPluginAsset;
use vova07\imperavi\Asset;
use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\JqueryAsset;

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
//    MomentAsset::class => [
//        'class' => \frontend\assets\MomentAsset::class
//    ],
    CheckboxColumnAsset::class => [
        'class' => KartikCheckboxColumnAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikCheckboxColumnAsset::class,
        ]
    ],
    GridToggleDataAsset::class => [
        'class' => KartikGridToggleDataAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikGridToggleDataAsset::class,
        ]
    ],
    GridFloatHeadAsset::class => [
        'class' => KartikGridFloatHeadAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            KartikGridFloatHeadAsset::class,
        ]
    ],
    JqueryAsset::class => [
        'js' => ['https://code.jquery.com/jquery-3.5.1.min.js']
    ],
    LanguageItemPluginAsset::class => [
        'class' => LajaxLanguageItemPluginAsset::class,
        'css' => [],
        'js' => [],
        'depends' => [
            LajaxLanguageItemPluginAsset::class,
        ]
    ]
//    BootstrapAsset::class => [
//        'class' => BootstrapGroupAsset::class
//    ],
//    BootstrapPluginAsset::class => [
//        'class' => BootstrapGroupAsset::class
//    ]

]);
