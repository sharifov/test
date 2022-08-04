<?php

use borales\extensions\phoneInput\PhoneInputAsset;
use dosamigos\ckeditor\CKEditorAsset;
use dosamigos\ckeditor\CKEditorWidgetAsset;
use dosamigos\datetimepicker\DateTimePickerAsset;
use dosamigos\multiselect\MultiSelectAsset;
use frontend\assets\CallBoxAsset;
use frontend\assets\CentrifugeAsset;
use frontend\assets\EditToolAsset;
use frontend\assets\groups\AllSharedAsset;
use frontend\assets\groups\AllSharedGroupAsset;
use frontend\assets\groups\BootstrapGroupAsset;
use frontend\assets\MomentAsset;
use frontend\assets\overridden\ImperaviAsset;
use frontend\assets\overridden\KartikActiveFormAsset;
use frontend\assets\overridden\KartikCheckboxColumnAsset;
use frontend\assets\overridden\KartikDialogBootstrapAsset;
use frontend\assets\overridden\KartikEditableAsset;
use frontend\assets\overridden\KartikEditablePjaxAsset;
use frontend\assets\overridden\KartikExportMenuAsset;
use frontend\assets\overridden\KartikGridExportAsset;
use frontend\assets\overridden\KartikGridResizeColumnsAsset;
use frontend\assets\overridden\KartikGridToggleDataAsset;
use frontend\assets\overridden\KartikGridViewAsset;
use frontend\assets\overridden\KDNJsonEditorAsset;
use frontend\assets\overridden\LajaxLanguageItemPluginAsset;
use frontend\assets\PageLoaderAsset;
use frontend\assets\TaskListAssets;
use frontend\assets\Timeline2Asset;
use frontend\assets\TimelineAsset;
use frontend\assets\UserShiftCalendarAsset;
use frontend\assets\WebAudioRecorder;
use frontend\assets\WebPhoneAsset;
use frontend\themes\gentelella_v2\assets\BootstrapProgressbar;
use frontend\themes\gentelella_v2\assets\ClientChatAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAllAsset;
use frontend\themes\gentelella_v2\assets\FontAwesomeAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaCrudGroupAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaGroupAsset;
use frontend\themes\gentelella_v2\assets\SwitcheryAsset;
use frontend\widgets\clientChat\ClientChatWidgetAsset;
use frontend\widgets\cronExpression\CronExpressionAssets;
use frontend\widgets\frontendWidgetList\userflow\assets\UserFlowWidgetAsset;
use frontend\widgets\newWebPhone\DeviceAsset;
use frontend\widgets\newWebPhone\NewWebPhoneAsset;
use frontend\widgets\notification\NotificationSocketAsset;
use kartik\base\WidgetAsset;
use kartik\bs4dropdown\DropdownAsset;
use kartik\date\DatePickerAsset;
use kartik\daterange\DateRangePickerAsset;
use kartik\dialog\DialogAsset;
use kartik\dialog\DialogYiiAsset;
use kartik\export\ExportColumnAsset;
use kartik\export\ExportMenuAsset;
use kartik\popover\PopoverXAsset;
use kartik\select2\Select2Asset;
use kartik\select2\Select2KrajeeAsset;
use kartik\select2\ThemeKrajeeAsset;
use kartik\select2\ThemeKrajeeBs4Asset;
use kartik\time\TimePickerAsset;
use kdn\yii2\assets\JsonEditorFullAsset;
use kdn\yii2\assets\JsonEditorMinimalistAsset;
use kivork\bootstrap4glyphicons\assets\GlyphiconAsset;
use lajax\translatemanager\bundles\FrontendTranslationAsset;
use lajax\translatemanager\bundles\FrontendTranslationPluginAsset;
use lajax\translatemanager\bundles\LanguageAsset;
use lajax\translatemanager\bundles\LanguageItemPluginAsset;
use lajax\translatemanager\bundles\LanguagePluginAsset;
use lajax\translatemanager\bundles\ScanPluginAsset;
use lajax\translatemanager\bundles\TranslateAsset;
use lajax\translatemanager\bundles\TranslateManagerAsset;
use lajax\translatemanager\bundles\TranslatePluginAsset;
use lajax\translatemanager\bundles\TranslationPluginAsset;
use modules\hotel\assets\HotelAsset;
use unclead\multipleinput\assets\MultipleInputAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\grid\GridViewAsset;
use yii\validators\ValidationAsset;
use yii\web\JqueryAsset;
use yii\widgets\ActiveFormAsset;
use yii\widgets\MaskedInputAsset;
use kivork\search\widgets\searchForm\SearchFormAssets;
use kivork\search\widgets\loader\LoaderAssets;
use kivork\search\widgets\flightResult\FlightResultsAssets;
use kivork\search\assets\SearchAssets;

Yii::setAlias('@webroot', __DIR__ . '/../../frontend/web');
Yii::setAlias('@web', '/');

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$appVersion = $params['release']['version'] ?? '';

return [
    'jsCompressor' => 'node_modules/.bin/gulp compress-js --gulpfile gulpfile.js --src {from} --dist {to}',

    'cssCompressor' => 'node_modules/.bin/gulp compress-css --gulpfile gulpfile.js --src {from} --dist {to}',

    'deleteSource' => true,

    'bundles' => [
        AllSharedAsset::class,
        AllSharedGroupAsset::class,
        GentelellaGroupAsset::class,

        GentelellaCrudGroupAsset::class,
        FontAwesomeAsset::class,
        GlyphiconAsset::class,
        BootstrapGroupAsset::class,
        BootstrapProgressbar::class,
        PageLoaderAsset::class,

        NotificationSocketAsset::class,
        CentrifugeAsset::class,
        ClientChatAsset::class,
        ClientChatWidgetAsset::class,

        CallBoxAsset::class,
        EditToolAsset::class,

        NewWebPhoneAsset::class,
        DeviceAsset::class,

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

//        KDNJsonEditorAsset::class,
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

        KartikCheckboxColumnAsset::class,
        KartikGridToggleDataAsset::class,
        \kartik\daterange\MomentAsset::class,
        MultiSelectAsset::class,
        DateTimePickerAsset::class,
        TimePickerAsset::class,
        LanguagePluginAsset::class,
        LanguageAsset::class,
        ScanPluginAsset::class,
        TranslationPluginAsset::class,
        FrontendTranslationAsset::class,
        FrontendTranslationPluginAsset::class,
        TranslateAsset::class,
        TranslateManagerAsset::class,
        TranslatePluginAsset::class,
        LajaxLanguageItemPluginAsset::class,

        CronExpressionAssets::class,
        HotelAsset::class,

        SwitcheryAsset::class,

        UserFlowWidgetAsset::class,
        UserShiftCalendarAsset::class,
        TaskListAssets::class,

//        MomentAsset::class
    ],

    'targets' => [

        'AllSharedAsset' => [
            'class' => AllSharedAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'all-shared.min.js',
            'css' => 'all-shared.min.css',
        ],

        'AllSharedGroupAsset' => [
            'class' => AllSharedGroupAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'all-shared-group.min.js',
            'css' => 'all-shared-group.min.css',
            'depends' => [
                AllSharedGroupAsset::class,
            ]
        ],

        'FontAwesomeAsset' => [
            'class' => FontAwesomeAsset::class,
            'basePath' => '@webroot/fontawesome/build',
            'baseUrl' => '@web/fontawesome/build',
            'js' => '',
            'css' => '',
            'depends' => [
                FontAwesomeAsset::class,
            ]
        ],

        'GlyphiconAsset' => [
            'class' => GlyphiconAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'glyphicon.min.js',
            'css' => 'glyphicon.min.css',
            'depends' => [
                GlyphiconAsset::class,
            ]
        ],

        'PageLoaderAsset' => [
            'class' => PageLoaderAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'page-loader.min.js',
            'css' => 'page-loader.min.css',
            'depends' => [
                PageLoaderAsset::class,
            ]
        ],

        'GentelellaGroupAsset' => [
            'class' => GentelellaGroupAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'gentelella.min.js',
            'css' => 'gentelella.min.css',
            'depends' => [GentelellaGroupAsset::class]
        ],

        'GentelellaCrudGroupAsset' => [
            'class' => GentelellaCrudGroupAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'gentelella-crud-group.min.js',
            'css' => 'gentelella-crud-group.min.css',
            'depends' => [GentelellaCrudGroupAsset::class]
        ],

        'NotificationSocketAsset' => [
            'class' => NotificationSocketAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'notification-socket.min.js',
            'css' => 'notification-socket.min.css',
            'depends' => [ NotificationSocketAsset::class ]
        ],
        'CentrifugeAsset' => [
            'class' => CentrifugeAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'centrifuge.min.js',
            'css' => 'centrifuge.min.css',
            'depends' => [ CentrifugeAsset::class ]
        ],
        'ClientChatWidgetAsset' => [
            'class' => ClientChatWidgetAsset::class,
            'basePath' => '@webroot/client_chat/build',
            'baseUrl' => '@web/client_chat/build',
            'js' => 'client_chat_widget.min.js',
            'css' => 'client_chat_widget.min.css',
            'depends' => [ ClientChatWidgetAsset::class ]
        ],
        'ClientChatAsset' => [
            'class' => ClientChatAsset::class,
            'basePath' => '@webroot/client_chat/build',
            'baseUrl' => '@web/client_chat/build',
            'js' => 'client_chat.min.js',
            'css' => 'client_chat.min.css',
            'depends' => [ ClientChatAsset::class ]
        ],

        'CallBoxAsset' => [
            'class' => CallBoxAsset::class,
            'basePath' => '@webroot/client_chat/build',
            'baseUrl' => '@web/client_chat/build',
            'js' => 'call_box.min.js',
            'css' => 'call_box.min.css',
            'depends' => [ CallBoxAsset::class ]
        ],
        'EditToolAsset' => [
            'class' => EditToolAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'edit_tool.min.js',
            'css' => 'edit_tool.min.css',
            'depends' => [ EditToolAsset::class ]
        ],
        'NewWebPhoneAsset' => [
            'class' => NewWebPhoneAsset::class,
            'basePath' => '@webroot/web_phone/build',
            'baseUrl' => '@web/web_phone/build',
            'js' => 'web_phone.min.' . $params['release']['version'] . '.js',
            'css' => 'web_phone.min.' . $params['release']['version'] . '.css',
            'depends' => [ NewWebPhoneAsset::class ]
        ],
        'DeviceAsset' => [
            'class' => DeviceAsset::class,
            'basePath' => '@webroot/web_phone/build',
            'baseUrl' => '@web/web_phone/build',
            'js' => 'device.min.' . $params['release']['version'] . '.js',
            'depends' => [ DeviceAsset::class ]
        ],
        'ActiveFormAsset' => [
            'class' => ActiveFormAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'yii-active-form.min.js',
            'css' => 'yii-active-form.min.css',
            'depends' => [ ActiveFormAsset::class ]
        ],
        'ValidationAsset' => [
            'class' => ValidationAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'yii-validation.min.js',
            'css' => 'yii-validation.min.css',
            'depends' => [ ValidationAsset::class ]
        ],
        'GridViewAsset' => [
            'class' => GridViewAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'yii-grid-view.min.js',
            'css' => 'yii-grid-view.min.css',
            'depends' => [ GridViewAsset::class ]
        ],

        'PopoverXAsset' => [
            'class' => PopoverXAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'popover-x-k.min.js',
            'css' => 'popover-x-k.min.css',
            'depends' => [PopoverXAsset::class],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikEditableAsset' => [
            'class' => KartikEditableAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'editable-k.min.js',
            'css' => 'editable-k.min.css',
            'depends' => [ KartikEditableAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikEditablePjaxAsset' => [
            'class' => KartikEditablePjaxAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'editable-pjax-k.min.js',
            'css' => 'editable-pjax-k.min.css',
            'depends' => [ KartikEditablePjaxAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikActiveFormAsset' => [
            'class' => KartikActiveFormAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'active-form-k.min.js',
            'css' => 'active-form-k.min.css',
            'depends' => [ KartikActiveFormAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'WidgetAsset' => [
            'class' => WidgetAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-widget-asset.min.js',
            'css' => 'kartik-widget-asset.min.css',
            'depends' => [ WidgetAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'ImperaviAsset' => [
            'class' => ImperaviAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'imperavi-asset.min.js',
            'css' => 'imperavi-asset.min.css',
            'depends' => [ ImperaviAsset::class ],
        ],

        'MultipleInputAsset' => [
            'class' => MultipleInputAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'multiple-input.min.js',
            'css' => 'multiple-input.min.css',
            'depends' => [ MultipleInputAsset::class ],
        ],

        'Select2Asset' => [
            'class' => Select2Asset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-select2.min.js',
            'css' => 'kartik-select2.min.css',
            'depends' => [ Select2Asset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'Select2KrajeeAsset' => [
            'class' => Select2KrajeeAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'krajee-select2.min.js',
            'css' => 'krajee-select2.min.css',
            'depends' => [ Select2KrajeeAsset::class, ThemeKrajeeBs4Asset::class, ThemeKrajeeAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'PhoneInputAsset' => [
            'class' => PhoneInputAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'phone-input.min.js',
            'css' => 'phone-input.min.css',
            'depends' => [ PhoneInputAsset::class ],
        ],
        'KartikDateRangePickerAsset' => [
            'class' => DateRangePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-date-range-picker.min.js',
            'css' => 'kartik-date-range-picker.min.css',
            'depends' => [ DateRangePickerAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'DatePickerAsset' => [
            'class' => DatePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'bootstrap-datepicker.min.js',
            'css' => 'bootstrap-datepicker.min.css',
            'depends' => [ DatePickerAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],

        'CKEditorWidgetAsset' => [
            'class' => CKEditorWidgetAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'ckeditor-widget.min.js',
            'css' => 'ckeditor-widget.min.css',
            'depends' => [ CKEditorWidgetAsset::class ],
        ],

        'Timeline2Asset' => [
            'class' => Timeline2Asset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'timeline2.min.js',
            'css' => 'timeline2.min.css',
            'depends' => [ Timeline2Asset::class ],
        ],

        'TimelineAsset' => [
            'class' => TimelineAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'timeline.min.js',
            'css' => 'timeline.min.css',
            'depends' => [ TimelineAsset::class ],
        ],

        'DosamigosDatepicker' => [
            'class' => \dosamigos\datepicker\DatePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dosamigos-datepicker.min.js',
            'css' => 'dosamigos-datepicker.min.css',
            'depends' => [ \dosamigos\datepicker\DatePickerAsset::class ],
        ],

//        'KDNJsonEditorAsset' => [
//            'class' => KDNJsonEditorAsset::class,
//            'basePath' => '@webroot/all_shared/build',
//            'baseUrl' => '@web/all_shared/build',
//            'js' => 'jsoneditor.min.js',
//            'css' => 'jsoneditor.min.css',
//            'depends' => [ KDNJsonEditorAsset::class, ],
//        ],
        'KartikExportMenuAsset' => [
            'class' => KartikExportMenuAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'export-menu.min.js',
            'css' => 'export-menu.min.css',
            'depends' => [ KartikExportMenuAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'ExportColumnAsset' => [
            'class' => ExportColumnAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'export-column.min.js',
            'css' => 'export-column.min.css',
            'depends' => [ ExportColumnAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'DropdownAsset' => [
            'class' => DropdownAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dropdown.min.js',
            'css' => 'dropdown.min.css',
            'depends' => [ DropdownAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'DialogAsset' => [
            'class' => DialogAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dialog.min.js',
            'css' => 'dialog.min.css',
            'depends' => [ DialogAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'DialogYiiAsset' => [
            'class' => DialogYiiAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dialog-yii.min.js',
            'css' => 'dialog-yii.min.css',
            'depends' => [ DialogYiiAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'BootstrapDialogAsset' => [
            'class' => KartikDialogBootstrapAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dialog-bootstrap.min.js',
            'css' => 'dialog-bootstrap.min.css',
            'depends' => [ KartikDialogBootstrapAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'BootstrapProgressbar,' => [
            'class' => BootstrapProgressbar::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'bootstrap-progressbar.min.js',
            'css' => 'bootstrap-progressbar.min.css',
            'depends' => [ BootstrapProgressbar::class ],
        ],
        'KartikGridViewAsset' => [
            'class' => KartikGridViewAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-gridview.min.js',
            'css' => 'kartik-gridview.min.css',
            'depends' => [ KartikGridViewAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikGridResizeColumnsAsset' => [
            'class' => KartikGridResizeColumnsAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-grid-resize-column.min.js',
            'css' => 'kartik-grid-resize-column.min.css',
            'depends' => [ KartikGridResizeColumnsAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikGridExportAsset' => [
            'class' => KartikGridExportAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-grid-export.min.js',
            'css' => 'kartik-grid-export.min.css',
            'depends' => [ KartikGridExportAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikCheckboxColumnAsset' => [
            'class' => KartikCheckboxColumnAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-grid-checkbox.min.js',
            'css' => 'kartik-grid-checkbox.min.css',
            'depends' => [ KartikCheckboxColumnAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikGridToggleDataAsset' => [
            'class' => KartikGridToggleDataAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-grid-toggle.min.js',
            'css' => 'kartik-grid-toggle.min.css',
            'depends' => [ KartikGridToggleDataAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'KartikMomentAsset' => [
            'class' => \kartik\daterange\MomentAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-moment.min.js',
            'css' => 'kartik-moment.min.css',
            'depends' => [ \kartik\daterange\MomentAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'TimePickerAsset' => [
            'class' => TimePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'kartik-timepicker.min.js',
            'css' => 'kartik-timepicker.min.css',
            'depends' => [ TimePickerAsset::class ],
            'bsPluginEnabled' => false,
            'bsDependencyEnabled' => false
        ],
        'MultiSelectAsset' => [
            'class' => MultiSelectAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dosamigos-multiselect.min.js',
            'css' => 'dosamigos-multiselect.min.css',
            'depends' => [ MultiSelectAsset::class ],
        ],
        'LanguagePluginAsset' => [
            'class' => LanguagePluginAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'language-plugin.min.js',
            'css' => 'language-plugin.min.css',
            'depends' => [ LanguagePluginAsset::class ],
        ],
        'ScanPluginAsset' => [
            'class' => ScanPluginAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'scan-plugin.min.js',
            'css' => 'scan-plugin.min.css',
            'depends' => [ ScanPluginAsset::class ],
        ],
        'TranslationPluginAsset' => [
            'class' => TranslationPluginAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'translation-plugin.min.js',
            'css' => 'translation-plugin.min.css',
            'depends' => [ TranslationPluginAsset::class ],
        ],
        'LanguageAsset' => [
            'class' => LanguageAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'language.min.js',
            'css' => 'language.min.css',
            'depends' => [ LanguageAsset::class ],
        ],
        'FrontendTranslationAsset' => [
            'class' => FrontendTranslationAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'frontend-translation.min.js',
            'css' => 'frontend-translation.min.css',
            'depends' => [ FrontendTranslationAsset::class ],
        ],
        'FrontendTranslationPluginAsset' => [
            'class' => FrontendTranslationPluginAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'frontend-translation-plugin.min.js',
            'css' => 'frontend-translation-plugin.min.css',
            'depends' => [ FrontendTranslationPluginAsset::class ],
        ],
        'TranslateAsset' => [
            'class' => TranslateAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'translate.min.js',
            'css' => 'translate.min.css',
            'depends' => [ TranslateAsset::class ],
        ],
        'TranslateManagerAsset' => [
            'class' => TranslateManagerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'translate-manager.min.js',
            'css' => 'translate-manager.min.css',
            'depends' => [ TranslateManagerAsset::class ],
        ],
        'TranslatePluginAsset' => [
            'class' => TranslatePluginAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'translate-plugin.min.js',
            'css' => 'translate-plugin.min.css',
            'depends' => [ TranslatePluginAsset::class ],
        ],
        'LajaxLanguageItemPluginAsset' => [
            'class' => LajaxLanguageItemPluginAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'lang-asset.min.js',
            'css' => 'lang-asset.min.css',
            'depends' => [ LajaxLanguageItemPluginAsset::class ],
        ],
        'DateTimePickerAsset' => [
            'class' => DateTimePickerAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'dosamigos-datetimepicker.min.js',
            'css' => 'dosamigos-datetimepicker.min.css',
            'depends' => [ DateTimePickerAsset::class ],
        ],
        'MaskedInputAsset' => [
            'class' => MaskedInputAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'maskedinput.min.js',
            'css' => 'maskedinput.min.css',
            'depends' => [ MaskedInputAsset::class ],
        ],
        'WebAudioRecorder' => [
            'class' => WebAudioRecorder::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'webaudiorecorder.min.js',
            'css' => 'webaudiorecorder.min.css',
            'depends' => [ WebAudioRecorder::class ],
        ],
        'WebPhoneAsset' => [
            'class' => WebPhoneAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'web-phone.min.js',
            'css' => 'web-phone.min.css',
            'depends' => [ WebPhoneAsset::class ],
        ],
//        'MomentAsset' => [
//            'class' => MomentAsset::class,
//            'basePath' => '@webroot/all_shared/build',
//            'baseUrl' => '@web/all_shared/build',
//            'js' => 'moment.min.js',
//            'css' => 'moment.min.css',
//            'depends' => [ MomentAsset::class ],
//        ],
        'CronExpressionAssets' => [
            'class' => CronExpressionAssets::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'cron-expression.min.js',
            'css' => 'cron-expression.min.css',
            'depends' => [ CronExpressionAssets::class ],
        ],
        'HotelAsset' => [
            'class' => HotelAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'hotel-asset.min.js',
            'css' => 'hotel-asset.min.css',
            'depends' => [ HotelAsset::class ],
        ],
        'UserFlowWidgetAsset' => [
            'class' => UserFlowWidgetAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'userflow.min.js',
            'css' => 'userflow.min.css',
            'depends' => [ UserFlowWidgetAsset::class ],
        ],
        'UserShiftCalendarAsset' => [
            'class' => UserShiftCalendarAsset::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'timeline.min.js',
            'css' => 'timeline.min.css',
            'depends' => [ UserShiftCalendarAsset::class ],
        ],
        'TaskListAssets' => [
            'class' => TaskListAssets::class,
            'basePath' => '@webroot/all_shared/build',
            'baseUrl' => '@web/all_shared/build',
            'js' => 'task-list.min.js',
            'css' => 'task-list.min.css',
            'depends' => [ TaskListAssets::class ],
        ],
    ],

    'assetManager' => [
        'basePath' => '@webroot/all_shared/build',
        'baseUrl' => '@web/all_shared/build',
        'bundles' => [
            yii\bootstrap\BootstrapAsset::class => [
                'class' => BootstrapGroupAsset::class,
                'depends' => [JqueryAsset::class]
            ],
            yii\bootstrap\BootstrapPluginAsset::class => [
                'class' => BootstrapGroupAsset::class,
                'depends' => [JqueryAsset::class]
            ],
            PopoverXAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            MultiSelectAsset::class => [
                'depends' => [
                    JqueryAsset::class,
                    BootstrapPluginAsset::class,
                ]
            ],
            \kartik\form\ActiveFormAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            DateRangePickerAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            \dosamigos\datepicker\DatePickerAsset::class => [
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class, AllSharedAsset::class]
            ],
            Select2KrajeeAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            Select2Asset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            KartikActiveFormAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            TimePickerAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            \kartik\daterange\MomentAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class]
            ],
            DateTimePickerAsset::class => [
                'baseUrl' => '@web/all_shared/build',
                'depends' => [
                    BootstrapGroupAsset::class
                ]
            ],
            ThemeKrajeeBs4Asset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            ThemeKrajeeAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            KartikEditablePjaxAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            DatePickerAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            CKEditorAsset::class => [
                'sourcePath' => null,
                'js' => [
                    'https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js',
                ],
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            ClientChatAsset::class => [
                'depends' => [JqueryAsset::class, BootstrapGroupAsset::class]
            ],
            WidgetAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [AllSharedAsset::class, JqueryAsset::class]
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
                    BootstrapGroupAsset::class,
                    AllSharedAsset::class,
                ]
            ],
            \kartik\editable\EditableAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class]
            ],
            ClientChatWidgetAsset::class => [
                'depends' => [
                    JqueryAsset::class,
                    AllSharedAsset::class,
                    MomentAsset::class
                ]
            ],
            KartikExportMenuAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [
                    JqueryAsset::class,
                    BootstrapGroupAsset::class,
                    AllSharedAsset::class,
                    DialogAsset::class
                ]
            ],
            KartikDialogBootstrapAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [AllSharedAsset::class, JqueryAsset::class]
            ],
            DialogAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [
                    JqueryAsset::class
                ]
            ],
            ExportMenuAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class]
            ],
            ExportColumnAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class]
            ],
            DialogYiiAsset::class => [
                'bsPluginEnabled' => false,
                'bsDependencyEnabled' => false,
                'depends' => [JqueryAsset::class]
            ],
//            GlyphiconAsset::class => [
//                'css' => [],
//                'js' => [],
//                'basePath' => null,
//                'sourcePath' => null,
//                'depends' => []
//            ],
            FontAwesomeAsset::class => [
                'depends' => [
                    JqueryAsset::class,
                    FontAwesomeAllAsset::class,
                    GlyphiconAsset::class,
                ]
            ],
            JqueryAsset::class => [
                'css' => [],
                'js' => [],
                'depends' => [],
                'basePath' => null,
                'sourcePath' => null
            ],
            \yii\bootstrap4\BootstrapAsset::class => [
                'css' => [],
                'js' => [],
                'depends' => [
                    JqueryAsset::class
                ],
                'basePath' => null,
                'sourcePath' => null
            ],
            BootstrapPluginAsset::class => [
                'css' => [],
                'js' => [],
                'depends' => [
                    JqueryAsset::class
                ],
                'basePath' => null,
                'sourcePath' => null
            ],
            \yii\jui\JuiAsset::class => [
                'css' => ['https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css'],
                'js' => ['https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'],
                'basePath' => null,
                'sourcePath' => null,
                'depends' => [
                    JqueryAsset::class
                ]
            ],
            \frontend\assets\ReactAsset::class => [
                'css' => [],
                'js' => [
                    ['https://unpkg.com/react@16.14.0/umd/react.production.min.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true],
                    ['https://unpkg.com/react-dom@16.14.0/umd/react-dom.production.min.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true],
                    ['https://unpkg.com/babel-standalone@6.26.0/babel.min.js'],
                ],
                'basePath' => null,
                'sourcePath' => null,
                'depends' => [],
            ],
            SearchFormAssets::class => false,
            LoaderAssets::class => false,
            FlightResultsAssets::class => false,
            SearchAssets::class => false
        ],
        'hashCallback' => static function ($path) {
            return hash('md4', $path);
        },
    ],
];
