<?php

use common\models\Employee;
use modules\fileStorage\src\services\access\FileStorageAccessService;
use modules\fileStorage\src\services\url\FileInfo;
use modules\fileStorage\src\services\url\QueryParams;
use modules\fileStorage\src\services\url\UrlGenerator;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\View;
use kartik\editable\Editable;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var View $this */
/** @var array $files */
/** @var string $uploadWidget */
/** @var UrlGenerator $urlGenerator */
/** @var QueryParams $queryParams  */
/** @var bool $canView  */
/** @var bool $canDelete  */

$countFiles = count($files);
$i = 1;
?>

<div class="x_panel">
    <div class="x_title">
        <?php Pjax::begin(['id' => 'pjax-file-count']); ?>
        <h2 class="file-storage-list-counter" data-count="<?php echo $countFiles ?>">
            Files (<span id="file-count-value"><?php echo $countFiles ?></span>)
        </h2>
         <?php Pjax::end() ?>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <?php echo $uploadWidget ?>
            </li>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: <?=Yii::$app->request->isPjax ? 'block' : 'none';?>">
        <?php Pjax::begin(['id' => 'pjax-file-list']); ?>
        <table class="table table-bordered file-storage-list">
            <tr>
                <th>#</th>
                <th>Filename</th>
                <th>Title</th>
                <th>Size</th>
                <th>Upload by</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($files as $file) : ?>
                <?php $shortName = StringHelper::truncate(Html::encode($file['name']), 30) ?>
                <?php $shortTitle = StringHelper::truncate(Html::encode($file['title']), 30) ?>
                <?php $linkView = $urlGenerator->generate(new FileInfo($file['name'], $file['path'], $file['uid'], $file['title'], $queryParams)) ?>

                <?php if (!empty($file['lead_id'])) : ?>
                    <?php $queryParamsContext = (new QueryParams(['context' => QueryParams::CONTEXT_LEAD, 'as_file' => true])) ?>
                <?php elseif (!empty($file['case_id'])) : ?>
                    <?php $queryParamsContext = (new QueryParams(['context' => QueryParams::CONTEXT_CASE, 'as_file' => true])) ?>
                <?php else : ?>
                    <?php $queryParamsContext = $queryParams ?>
                <?php endif ?>
                <?php $linkDownload = $urlGenerator->generate(new FileInfo($file['name'], $file['path'], $file['uid'], $file['title'], $queryParamsContext)) ?>

                <tr class="file-box" data-item="<?php echo $i ?>">
                    <td><?php echo $i ?></td>
                    <td>
                        <?php if ($canView) : ?>
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
                    <td style="max-width: 250px; overflow: hidden;" title="<?php echo Html::encode($file['title']) ?>">
                        <?php if (FileStorageAccessService::canEditTitleFile()) : ?>
                            <?= Editable::widget([
                                'name' => 'file_title[' . $file['id'] . ']',
                                'asPopover' => false,
                                'value' => Html::encode($file['title']),
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
                        <?php echo Yii::$app->formatter->asShortSize($file['size'], 2) ?>
                    </td>
                    <td>
                        <?php if ($file['user_id']) : ?>
                            <i class="fa fa-user-secret"></i> <?php echo ($user = Employee::findOne((int) $file['user_id'])) ? $user->username : '' ?>
                        <?php else : ?>
                            <i class="fa fa-user"></i> Client
                        <?php endif ?>
                    </td>
                    <td>
                        <?php echo Yii::$app->formatter->asByUserDateTime($file['created_dt']) ?>
                    </td>
                    <td>
                        <ul class="nav navbar-right panel_toolbox" style="float: none;">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <?php if ($canView) : ?>
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
                                    <?php if ($canDelete) : ?>
                                        <div class="dropdown-divider"></div>
                                        <?= Html::a('<i class="fa fa-times"></i> Delete', null, [
                                            'class' => 'dropdown-item text-danger js-delete-file-btn',
                                            'data-pjax' => '0',
                                            'data-file_id' => $file['id'],
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
        <?php Pjax::end() ?>

    </div>
</div>

<?php
$canView = $canView ? 'true' : 'false';
$urlDeleteFile = Url::to(['/file-storage/file-storage/delete-ajax']);
$js = <<<JS

$(document).on('click', '.js-delete-file-btn', function(e){
    e.preventDefault();

    if(!confirm('Are you sure you want to delete the file?')) {
        return false;
    }

    let btn = $(this);

    $.ajax({
        url: '{$urlDeleteFile}',
        type: 'POST',
        data: {file_id: btn.data('file_id')},
        dataType: 'json'
    })
    .done(function(dataResponse) {
        if (dataResponse.status === 1) {
            pjaxReload({container: '#pjax-file-list'});
            createNotify('Success', dataResponse.message, 'success');

            let counter = $('#file-count-value');
            let count = parseInt(counter.text());
            count--;
            counter.text(count);
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
    .always(function(jqXHR, textStatus, errorThrown) {});
});

function addFileToFileStorageList() {
    pjaxReload({container: '#pjax-file-list'});
    $(".modal-backdrop").remove();

    let counter = $('#file-count-value');
    let count = parseInt(counter.text());
    count++;
    counter.text(count);
}
JS;
$this->registerJs($js, View::POS_END);

$css = <<<CSS
    .fileinput-remove span {
        margin-right: 3px;
        line-height: 21px;
    }
    .file-footer-buttons .kv-file-upload{
        display: none;
    }
CSS;
$this->registerCss($css);
