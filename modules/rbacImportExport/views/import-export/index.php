<?php

use modules\rbacImportExport\src\formatters\FileSizeFormatter;
use yii\grid\ActionColumn;
use modules\rbacImportExport\src\entity\AuthImportExport;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\rbacImportExport\src\entity\search\AuthImportExportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Auth Import Exports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-import-export-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-file-export"></i> Export Data', \yii\helpers\Url::toRoute('/rbac-import-export/import-export/export-view'), ['class' => 'btn btn-success btn-export']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'aie_id',
            [
                'attribute' => 'aie_type',
                'filter' => AuthImportExport::getTypeList(),
                'value' => static function (AuthImportExport $model) {
                    return $model->getTypeName();
                }
            ],
            'aie_cnt_roles',
            'aie_cnt_permissions',
            'aie_cnt_rules',
            'aie_cnt_childs',
            'aie_file_name',
            [
                'attribute' => 'aie_file_size',
                'value' => static function (AuthImportExport $model) {
                    return FileSizeFormatter::asSize($model->aie_file_size);
                },
            ],
            'aie_created_dt',
            'aie_user_id:userName',

            ['class' => ActionColumn::class, 'template' => '{view} {download} {delete}', 'buttons' => [
                'download' => static function ($url, AuthImportExport $model, $key) {
                    return Html::a('<i class="fas fa-download"></i>', ['/rbac-import-export/import-export/download', 'id' => $model->aie_id], ['data-pjax' => 0]);
                }
            ]],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

    <?php $js = <<<JS
    $('.btn-export').on('click', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        
        let modal = $('#modal-sm');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('RBAC Export Data');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            modal.modal({
                backdrop: 'static',
                show: true
            });
        });
    });
JS;

    ?>

</div>
