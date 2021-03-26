<?php

use common\models\Employee;
use kartik\editable\Editable;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\services\access\FileStorageAccessService;
use modules\fileStorage\src\services\url\FileInfo;
use modules\fileStorage\src\services\url\QueryParams;
use modules\fileStorage\src\services\url\UrlGenerator;
use modules\order\src\entities\order\Order;
use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var Order $order */
/* @var FileOrder[]|null $orderFiles */
/* @var UrlGenerator $urlGenerator */

$queryParamsView = (new QueryParams(['guard_enabled' => false]));
$queryParamsAsFile = (new QueryParams(['guard_enabled' => false, 'as_file' => true]));
$i = 1;
?>

<div class="order-view-file-box">
    <?php Pjax::begin(['id' => 'pjax-order-file-' . $order->or_id, 'enablePushState' => false, 'timeout' => 10000])?>

        <div class="x_panel x_panel_file">
            <div class="x_title">
                <h2><i class="fas fa-file-pdf-o"></i> File List</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <div class="x_panel">
                    <div class="x_title"></div>
                    <div class="x_content" style="display: block">
                        <table class="table table-bordered file-storage-list">
                            <tr>
                                <th>#</th>
                                <th>Filename</th>
                                <th>Title</th>
                                <th>Size</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                            <?php foreach ($orderFiles as $fileOrder) : ?>
                                <?php if (!$file = $fileOrder->file) : ?>
                                    <?php continue ?>
                                <?php endif ?>

                                <?php $shortName = StringHelper::truncate(Html::encode($file->fs_name), 30) ?>
                                <?php $shortTitle = StringHelper::truncate(Html::encode($file->fs_title), 30) ?>
                                <?php $linkView = $urlGenerator->generate(FileInfo::byFileStorage($file, $queryParamsView)) ?>
                                <?php $linkDownload = $urlGenerator->generate(FileInfo::byFileStorage($file, $queryParamsAsFile)) ?>

                                <tr class="file-box" data-item="<?php echo $i ?>">
                                    <td><?php echo $i ?></td>
                                    <td>
                                        <?php if (Auth::can('file-storage/view')) : ?>
                                            <?php echo
                                                Html::a(
                                                    $shortName,
                                                    $linkDownload,
                                                    [
                                                        'target' => 'blank',
                                                        'data-pjax' => '0',
                                                        'data' => [
                                                            'confirm' => 'Are you sure you want to download the file?'
                                                        ],
                                                    ]
                                                )
                                            ?>
                                        <?php else : ?>
                                            <?php echo $shortName ?>
                                        <?php endif ?>
                                    </td>
                                    <td style="max-width: 250px; overflow: hidden;" title="<?php echo Html::encode($file->fs_title) ?>">
                                        <?php if (FileStorageAccessService::canEditTitleFile()) : ?>
                                            <?php echo Editable::widget([
                                                'name' => 'file_title[' . $file->fs_id . ']',
                                                'asPopover' => false,
                                                'value' => Html::encode($file->fs_title),
                                                'header' => 'File title',
                                                'size' => 'sm',
                                                'inputType' => Editable::INPUT_TEXT,
                                                'buttonsTemplate' => '{submit}',
                                                'pluginEvents' => [
                                                    'editableSuccess' => "function(event, val, form, data) {
                                                        $(this).parent('td').attr('title', val);
                                                    }",
                                                ],
                                                'inlineSettings' => [
                                                    'templateBefore' => '<div class="editable-pannel">{loading}',
                                                    'templateAfter' => '{buttons}{close}</div>'
                                                ],
                                                'options' => [
                                                    'class' => 'form-control',
                                                    'style' => 'width:150px;',
                                                    'placeholder' => 'File title',
                                                    'resetButton' => '<i class="fa fa-ban"></i>'
                                                ],
                                                'formOptions' => [
                                                    'action' => Url::toRoute(['/file-storage/file-storage/title-update'])
                                                ]
                                            ]) ?>
                                        <?php else : ?>
                                            <span><?php echo $shortTitle ?></span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php echo Yii::$app->formatter->asShortSize($file->fs_size, 2) ?>
                                    </td>
                                    <td>
                                        <?php echo Yii::$app->formatter->asByUserDateTime($file->fs_created_dt) ?>
                                    </td>
                                    <td>
                                        <ul class="nav navbar-right panel_toolbox" style="float: none;">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                                    <?php if (Auth::can('file-storage/view')) : ?>
                                                        <?= Html::a('<i class="fa fa-eye"></i> View', $linkView, [
                                                            'class' => 'dropdown-item js-vew-file-btn',
                                                            'target' => 'blank',
                                                            'data-pjax' => '0',
                                                        ]) ?>
                                                        <?= Html::a('<i class="fa fa-download"></i> Download', $linkDownload, [
                                                            'class' => 'dropdown-item text-success js-download-file-btn',
                                                            'target' => 'blank',
                                                            'data-pjax' => '0',
                                                            'data' => [
                                                                'confirm' => 'Are you sure you want to download the file?'
                                                            ],
                                                        ]) ?>
                                                    <?php endif ?>
                                                    <?php if (FileStorageAccessService::canDeleteFile()) : ?>
                                                        <div class="dropdown-divider"></div>
                                                        <?= Html::a('<i class="fa fa-times"></i> Delete', null, [
                                                            'class' => 'dropdown-item text-danger js-delete-file-btn',
                                                            'data-pjax' => '0',
                                                            'data-file_id' => $file->fs_id,
                                                            'data-url' => Url::to(['/file-storage/file-storage/delete-ajax']),
                                                            'data-pjax_box' => 'pjax-order-file-' . $order->or_id
                                                        ]) ?>
                                                    <?php endif ?>
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <?php $i++ ?>
                            <?php endforeach ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php Pjax::end() ?>
</div>

<?php
$js = <<<JS
    $(document).on('click', '.js-delete-file-btn', function(e){
        e.preventDefault();
        if(!confirm('Are you sure you want to delete the file?')) {
            return false;
        }
        let btn = $(this);
        let fileId = btn.data('file_id');
        let url = btn.data('url');
        let containerId = '#' + btn.data('pjax_box');
        $('#preloader').removeClass('d-none');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {file_id: fileId},
            dataType: 'json'
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {
                pjaxReload({container: containerId});  
                createNotify('Success', dataResponse.message, 'success');
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log({jqXHR : jqXHR, textStatus : textStatus, errorThrown : errorThrown}); 
            createNotify('Error', 'Server error. Try again later.', 'error');
        })
        .always(function() {
            $('#preloader').addClass('d-none');
        });
    });
JS;
$this->registerJs($js, yii\web\View::POS_END);

$css = <<<CSS
    .x_panel_file {
        background-color: #e3ebf3;
    }
CSS;
$this->registerCss($css);
