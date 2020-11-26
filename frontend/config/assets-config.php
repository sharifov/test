<?php
use borales\extensions\phoneInput\PhoneInputAsset;
use dosamigos\ckeditor\CKEditorAsset;
use dosamigos\ckeditor\CKEditorWidgetAsset;
use frontend\assets\CallBoxAsset;
use frontend\assets\CentrifugeAsset;
use frontend\assets\EditToolAsset;
use frontend\assets\groups\AllSharedAsset;
use frontend\assets\groups\AllSharedGroupAsset;
use frontend\assets\MomentAsset;
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
use frontend\assets\Timeline2Asset;
use frontend\assets\TimelineAsset;
use frontend\assets\WebAudioRecorder;
use frontend\assets\WebPhoneAsset;
use frontend\themes\gentelella_v2\assets\ClientChatAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAllAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaCrudGroupAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaGroupAsset;
use frontend\widgets\clientChat\ClientChatWidgetAsset;
use frontend\widgets\newWebPhone\NewWebPhoneGroupAsset;
use frontend\widgets\notification\NotificationSocketAsset;
use kartik\base\WidgetAsset;
use kartik\bs4dropdown\DropdownAsset;
use kartik\date\DatePickerAsset;
use kartik\daterange\DateRangePickerAsset;
use kartik\dialog\DialogAsset;
use kartik\dialog\DialogBootstrapAsset;
use kartik\dialog\DialogYiiAsset;
use kartik\export\ExportColumnAsset;
use kartik\export\ExportMenuAsset;
use kartik\popover\PopoverXAsset;
use kartik\select2\Select2Asset;
use kartik\select2\Select2KrajeeAsset;
use kartik\select2\ThemeKrajeeAsset;
use kartik\select2\ThemeKrajeeBs4Asset;
use kdn\yii2\assets\JsonEditorFullAsset;
use kdn\yii2\assets\JsonEditorMinimalistAsset;
use kivork\bootstrap4glyphicons\assets\GlyphiconAsset;
use unclead\multipleinput\assets\MultipleInputAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\grid\GridViewAsset;
use yii\validators\ValidationAsset;
use yii\web\JqueryAsset;
use yii\widgets\ActiveFormAsset;
use yii\widgets\MaskedInputAsset;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);
$appVersion = $params['release']['version'] ?? '';

Yii::setAlias('@webroot', __DIR__ . '/../../frontend/web');
Yii::setAlias('@web', '/');

return [
    'jsCompressor' => 'gulp compress-js --gulpfile gulpfile.js --src {from} --dist {to}',

    'cssCompressor' => 'gulp compress-css --gulpfile gulpfile.js --src {from} --dist {to}',

    'bundles' => [
        AllSharedAsset::class,
        AllSharedGroupAsset::class,
        GentelellaGroupAsset::class,

        GentelellaCrudGroupAsset::class,
        FontAwesomeAsset::class,
        GlyphiconAsset::class,

        NotificationSocketAsset::class,
        CentrifugeAsset::class,
        ClientChatAsset::class,
        ClientChatWidgetAsset::class,

        CallBoxAsset::class,
        EditToolAsset::class,

        NewWebPhoneGroupAsset::class,

        ActiveFormAsset::class,
        ValidationAsset::class,
        GridViewAsset::class,

        PopoverXAsset::class,
        KartikEditableAsset::class,
        KartikEditablePjaxAsset::class,
        KartikActiveFormAsset::class,
        WidgetAsset::class,
        ImperaviAsset::class,

        MultipleInputAsset::class,

        Select2KrajeeAsset::class,
        ThemeKrajeeBs4Asset::class,
        ThemeKrajeeAsset::class,
        Select2Asset::class,
        PhoneInputAsset::class,
        DateRangePickerAsset::class,
        DatePickerAsset::class,
        CKEditorAsset::class,
        CKEditorWidgetAsset::class,
        \dosamigos\datepicker\DatePickerAsset::class,

        Timeline2Asset::class,
        TimelineAsset::class,

        KDNJsonEditorAsset::class,
        KartikExportMenuAsset::class,
        ExportColumnAsset::class,
        DropdownAsset::class,
        DialogAsset::class,
        KartikDialogBootstrapAsset::class,
        KartikGridViewAsset::class,
        KartikGridResizeColumnsAsset::class,

        MaskedInputAsset::class,
        DialogYiiAsset::class,
        KartikGridExportAsset::class,
        WebAudioRecorder::class,
        WebPhoneAsset::class,
    ],

    'targets' => [

        'FontAwesomeAsset' => [
            'class' => FontAwesomeAsset::class,
            'basePath' => '@webroot/fontawesome/build',
            'baseUrl' => '@web/fontawesome/build',
            'js' => 'fontawesome-{hash}.js',
            'css' => 'fontawesome-{hash}.css',
            'depends' => [
                FontAwesomeAsset::class,
            ]
        ],

        'GlyphiconAsset' => [
            'class' => GlyphiconAsset::class,
            'basePath' => '@webroot/fontawesome/build',
            'baseUrl' => '@web/fontawesome/build',
            'js' => 'glyphicon-{hash}.js',
            'css' => 'glyphicon-{hash}.css',
            'depends' => [
                GlyphiconAsset::class,
            ]
        ],

        'AllSharedAsset' => [
            'class' => AllSharedAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'all-shared-{hash}.js',
            'css' => 'all-shared-{hash}.css',
        ],

        'AllSharedGroupAsset' => [
            'class' => AllSharedGroupAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'all-shared-group-{hash}.js',
            'css' => 'all-shared-group-{hash}.css',
            'depends' => [
                AllSharedGroupAsset::class,
            ]
        ],

        'GentelellaGroupAsset' => [
            'class' => GentelellaGroupAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'gentelella-{hash}.js',
            'css' => 'gentelella-{hash}.css',
            'depends' => [GentelellaGroupAsset::class]
        ],

        'GentelellaCrudGroupAsset' => [
            'class' => GentelellaCrudGroupAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'gentelella-crud-group-{hash}.js',
            'css' => 'gentelella-crud-group-{hash}.css',
            'depends' => [GentelellaCrudGroupAsset::class]
        ],

//        'FontAwesomeAsset' => [
//            'class' => FontAwesomeAsset::class,
//            'basePath' => '@webroot/fontawesome/build',
//            'baseUrl' => '@web/fontawesome/build',
//            'js' => 'fontawesome-{hash}.js',
//            'css' => 'fontawesome-{hash}.css',
//            'depends' => [
//                FontAwesomeAsset::class,
//                GlyphiconAsset::class,
//            ]
//        ],
        'NotificationSocketAsset' => [
            'class' => NotificationSocketAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'notification-socket-{hash}.js',
            'css' => 'notification-socket-{hash}.css',
            'depends' => [ NotificationSocketAsset::class ]
        ],
        'CentrifugeAsset' => [
            'class' => CentrifugeAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'centrifuge-{hash}.js',
            'css' => 'centrifuge-{hash}.css',
            'depends' => [ CentrifugeAsset::class ]
        ],
        'ClientChatWidgetAsset' => [
            'class' => ClientChatWidgetAsset::class,
            'basePath' => '@webroot/client_chat/build',
            'baseUrl' => '@web/client_chat/build',
            'js' => 'client_chat_widget-{hash}.js',
            'css' => 'client_chat_widget-{hash}.css',
            'depends' => [ ClientChatWidgetAsset::class ]
        ],
        'ClientChatAsset' => [
            'class' => ClientChatAsset::class,
            'basePath' => '@webroot/client_chat/build',
            'baseUrl' => '@web/client_chat/build',
            'js' => 'client_chat-{hash}.js',
            'css' => 'client_chat-{hash}.css',
            'depends' => [ ClientChatAsset::class ]
        ],

        'CallBoxAsset' => [
            'class' => CallBoxAsset::class,
            'basePath' => '@webroot/client_chat/build',
            'baseUrl' => '@web/client_chat/build',
            'js' => 'call_box-{hash}.js',
            'css' => 'call_box-{hash}.css',
            'depends' => [ CallBoxAsset::class ]
        ],
        'EditToolAsset' => [
            'class' => EditToolAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'edit_tool-{hash}.js',
            'css' => 'edit_tool-{hash}.css',
            'depends' => [ EditToolAsset::class ]
        ],
        'NewWebPhoneGroupAsset' => [
            'class' => NewWebPhoneGroupAsset::class,
            'basePath' => '@webroot/web_phone/build',
            'baseUrl' => '@web/web_phone/build',
            'js' => 'web_phone-{hash}.js',
            'css' => 'web_phone-{hash}.css',
            'depends' => [ NewWebPhoneGroupAsset::class ]
        ],
        'ActiveFormAsset' => [
            'class' => ActiveFormAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'yii-active-form-{hash}.js',
            'css' => 'yii-active-form-{hash}.css',
            'depends' => [ ActiveFormAsset::class ]
        ],
        'ValidationAsset' => [
            'class' => ValidationAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'yii-validation-{hash}.js',
            'css' => 'yii-validation-{hash}.css',
            'depends' => [ ValidationAsset::class ]
        ],
        'GridViewAsset' => [
            'class' => GridViewAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'yii-grid-view-{hash}.js',
            'css' => 'yii-grid-view-{hash}.css',
            'depends' => [ GridViewAsset::class ]
        ],

        'PopoverXAsset' => [
            'class' => PopoverXAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'popover-x-k-{hash}.js',
            'css' => 'popover-x-k-{hash}.css',
            'depends' => [PopoverXAsset::class],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikEditableAsset' => [
            'class' => KartikEditableAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'editable-k-{hash}.js',
            'css' => 'editable-k-{hash}.css',
            'depends' => [ KartikEditableAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikEditablePjaxAsset' => [
            'class' => KartikEditablePjaxAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'editable-pjax-k-{hash}.js',
            'css' => 'editable-pjax-k-{hash}.css',
            'depends' => [ KartikEditablePjaxAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikActiveFormAsset' => [
            'class' => KartikActiveFormAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'active-form-k-{hash}.js',
            'css' => 'active-form-{hash}.css',
            'depends' => [ KartikActiveFormAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'WidgetAsset' => [
            'class' => WidgetAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-widget-asset-{hash}.js',
            'css' => 'kartik-widget-asset-{hash}.css',
            'depends' => [ WidgetAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'ImperaviAsset' => [
            'class' => ImperaviAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'imperavi-asset-{hash}.js',
            'css' => 'imperavi-asset-{hash}.css',
            'depends' => [ ImperaviAsset::class ],
        ],

        'MultipleInputAsset' => [
            'class' => MultipleInputAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'multiple-input-{hash}.js',
            'css' => 'multiple-input-{hash}.css',
            'depends' => [ MultipleInputAsset::class ],
        ],

        'Select2Asset' => [
            'class' => Select2Asset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-select2-{hash}.js',
            'css' => 'kartik-select2-{hash}.css',
            'depends' => [ Select2Asset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'Select2KrajeeAsset' => [
            'class' => Select2KrajeeAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'krajee-select2-{hash}.js',
            'css' => 'krajee-select2-{hash}.css',
            'depends' => [ ThemeKrajeeBs4Asset::class, ThemeKrajeeAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'PhoneInputAsset' => [
            'class' => PhoneInputAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'phone-input-{hash}.js',
            'css' => 'phone-input-{hash}.css',
            'depends' => [ PhoneInputAsset::class ],
        ],
        'KartikDateRangePickerAsset' => [
            'class' => DateRangePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-date-range-picker-{hash}.js',
            'css' => 'kartik-date-range-picker-{hash}.css',
            'depends' => [ DateRangePickerAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'DatePickerAsset' => [
            'class' => DatePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'bootstrap-datepicker-{hash}.js',
            'css' => 'bootstrap-datepicker-{hash}.css',
            'depends' => [ DatePickerAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'CKEditorWidgetAsset' => [
            'class' => CKEditorWidgetAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'ckeditor-widget-{hash}.js',
            'css' => 'ckeditor-widget-{hash}.css',
            'depends' => [ CKEditorWidgetAsset::class ],
        ],

        'Timeline2Asset' => [
            'class' => Timeline2Asset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'timeline2-{hash}.js',
            'css' => 'timeline2-{hash}.css',
            'depends' => [ Timeline2Asset::class ],
        ],

        'TimelineAsset' => [
            'class' => TimelineAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'timeline-{hash}.js',
            'css' => 'timeline-{hash}.css',
            'depends' => [ TimelineAsset::class ],
        ],

        'DosamigosDatepicker' => [
            'class' => \dosamigos\datepicker\DatePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dosamigos-datepicker-{hash}.js',
            'css' => 'dosamigos-datepicker-{hash}.css',
            'depends' => [ \dosamigos\datepicker\DatePickerAsset::class ],
        ],

        'KDNJsonEditorAsset' => [
            'class' => KDNJsonEditorAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'jsoneditor-{hash}.js',
            'css' => 'jsoneditor-{hash}.css',
            'depends' => [ KDNJsonEditorAsset::class, ],
        ],
        'KartikExportMenuAsset' => [
            'class' => KartikExportMenuAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'export-menu-{hash}.js',
            'css' => 'export-menu-{hash}.css',
            'depends' => [ KartikExportMenuAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'ExportColumnAsset' => [
            'class' => ExportColumnAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'export-column-{hash}.js',
            'css' => 'export-column-{hash}.css',
            'depends' => [ ExportColumnAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'DropdownAsset' => [
            'class' => DropdownAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dropdown-{hash}.js',
            'css' => 'dropdown-{hash}.css',
            'depends' => [ DropdownAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'DialogAsset' => [
            'class' => DialogAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dialog-{hash}.js',
            'css' => 'dialog-{hash}.css',
            'depends' => [ DialogAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'DialogYiiAsset' => [
            'class' => DialogYiiAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dialog-yii-{hash}.js',
            'css' => 'dialog-yii-{hash}.css',
            'depends' => [ DialogYiiAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'BootstrapDialogAsset' => [
            'class' => KartikDialogBootstrapAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dialog-bootstrap-{hash}.js',
            'css' => 'dialog-bootstrap-{hash}.css',
            'depends' => [ KartikDialogBootstrapAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikGridViewAsset' => [
            'class' => KartikGridViewAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-gridview-{hash}.js',
            'css' => 'kartik-gridview-{hash}.css',
            'depends' => [ KartikGridViewAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikGridResizeColumnsAsset' => [
            'class' => KartikGridResizeColumnsAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-grid-resize-column-{hash}.js',
            'css' => 'kartik-grid-resize-column-{hash}.css',
            'depends' => [ KartikGridResizeColumnsAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikGridExportAsset' => [
            'class' => KartikGridExportAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-grid-export-{hash}.js',
            'css' => 'kartik-grid-export-{hash}.css',
            'depends' => [ KartikGridExportAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'MaskedInputAsset' => [
            'class' => MaskedInputAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'maskedinput-{hash}.js',
            'css' => 'maskedinput-{hash}.css',
            'depends' => [ MaskedInputAsset::class ],
        ],
        'WebAudioRecorder' => [
            'class' => WebAudioRecorder::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'webaudiorecorder-{hash}.js',
            'css' => 'webaudiorecorder-{hash}.css',
            'depends' => [ WebAudioRecorder::class ],
        ],
        'WebPhoneAsset' => [
            'class' => WebPhoneAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'web-phone-{hash}.js',
            'css' => 'web-phone-{hash}.css',
            'depends' => [ WebPhoneAsset::class ],
        ]
    ],

    'assetManager' => [
        'basePath' => '@webroot/all_shared/build',
        'baseUrl' => '@web/all_shared/build',
        'bundles' => [
            yii\bootstrap\BootstrapAsset::class => [
                'sourcePath' => '@npm/bootstrap/dist',
                'css' => [
                    'css/bootstrap.css'
                ],
            ],
            yii\bootstrap\BootstrapPluginAsset::class => [
                'class' => BootstrapPluginAsset::class,
                'sourcePath' => '@npm/bootstrap/dist',
                'js' => [
                    'js/bootstrap.bundle.js'
                ],
                'depends' => [],
            ],
            PopoverXAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            PhoneInputAsset::class => [
                'depends' => []
            ],
            \kartik\form\ActiveFormAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            DateRangePickerAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [
                    MomentAsset::class
                ]
            ],
            \dosamigos\datepicker\DatePickerAsset::class => [
                'depends' => [JqueryAsset::class]
            ],
            Select2KrajeeAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            Select2Asset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class]
            ],
            KartikActiveFormAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            ThemeKrajeeBs4Asset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            ThemeKrajeeAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            KartikEditablePjaxAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            DatePickerAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            CKEditorAsset::class => [
                'sourcePath' => null,
                'js' => [
                    'https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js',
                ],
                'depends' => []
            ],
            ClientChatAsset::class => [
                'depends' => [JqueryAsset::class]
            ],
            WidgetAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [AllSharedAsset::class]
            ],
            JsonEditorFullAsset::class => [
                'class' => KDNJsonEditorAsset::class
            ],
            JsonEditorMinimalistAsset::class => [
                'class' => KDNJsonEditorAsset::class
            ],
            KartikEditableAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [
                    AllSharedAsset::class
                ]
            ],
            \kartik\editable\EditableAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            ClientChatWidgetAsset::class => [
                'depends' => [
                    AllSharedAsset::class,
                    MomentAsset::class
                ]
            ],
            KartikExportMenuAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [
                    AllSharedAsset::class,
                    DialogAsset::class
                ]
            ],
            KartikDialogBootstrapAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [AllSharedAsset::class]
            ],
            DialogAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [
//                    AllSharedAsset::class,
                ]
            ],
            ExportMenuAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            ExportColumnAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            DialogYiiAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => []
            ],
            FontAwesomeAsset::class => [
                'depends' => [
                    FontAwesomeAllAsset::class,
                    GlyphiconAsset::class,
                ]
            ]
        ],
        'hashCallback' => static function ($path) use ($appVersion) {
            return hash('md4', $path . $appVersion);
        },
    ],
];
