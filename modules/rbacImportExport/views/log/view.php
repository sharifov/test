<?php

use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\formatters\FileSizeFormatter;
use modules\rbacImportExport\src\helpers\RbacDataHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\rbacImportExport\src\entity\AuthImportExport */

$this->title = $model->aie_file_name;
$this->params['breadcrumbs'][] = ['label' => 'RBAC Import Exports', 'url' => ['/rbac-import-export/log/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$userModel = Yii::createObject(Yii::$app->user->identityClass);
?>
<div class="auth-import-export-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-download"></i> Download', ['download', 'id' => $model->aie_id], ['class' => 'btn btn-primary']) ?>
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
			[
				'attribute' => 'aie_type',
				'value' => static function (AuthImportExport $model) {
					return $model->getTypeName();
				}
			],
            'aie_cnt_roles',
            'aie_cnt_permissions',
            'aie_cnt_rules',
            'aie_cnt_child',
            'aie_file_name',
			[
				'attribute' => 'aie_file_size',
				'value' => static function (AuthImportExport $model) {
					return FileSizeFormatter::asSize($model->aie_file_size);
				},
			],
            'aie_created_dt',
			[
				'attribute' => 'aie_user_id',
				'value' => static function (AuthImportExport $model) use ($userModel) {
					if ($entity = $userModel::findOne($model->aie_user_id)) {
						return $entity->username;
					}
					return $model->aie_user_id;
				},
			],
            [
                'attribute' => 'aie_data',
                'value' => static function (AuthImportExport $model) {
                    return \yii\helpers\VarDumper::dumpAsString(RbacDataHelper::decode($model->aie_data) );
                }
            ],
        ],
    ]) ?>

</div>
