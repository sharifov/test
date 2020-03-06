<?php

use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\formatters\FileSizeFormatter;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\rbacImportExport\src\entity\AuthImportExport */

$this->title = $model->aie_file_name;
$this->params['breadcrumbs'][] = ['label' => 'Auth Import Exports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-import-export-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-download"></i> Download', ['import-export/download', 'id' => $model->aie_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> Delete', ['delete', 'id' => $model->aie_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'aie_id',
            'aie_type',
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
            'aie_data_json',
        ],
    ]) ?>

</div>
