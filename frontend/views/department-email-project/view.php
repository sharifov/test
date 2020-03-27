<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentEmailProject */

$this->title = $model->depProject->name . ' - ' .$model->dep_id;
$this->params['breadcrumbs'][] = ['label' => 'Department Email Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="department-email-project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->dep_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->dep_id], [
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
            'dep_id',
            'dep_email',
            'emailList.el_email',
			[
				'attribute' => 'dep_project_id',
				'value' => static function (\common\models\DepartmentEmailProject $model) {
					return $model->depProject ? '<span class="badge">' . Html::encode($model->depProject->name) . '</span>' : '-';
				},
				'filter' => \common\models\Project::getList(true),
				'format' => 'raw',
			],
            'dep_dep_id:department',
			[
				'attribute' => 'dep_source_id',
				'value' => static function (\common\models\DepartmentEmailProject $model) {
					return $model->depSource ? $model->depSource->name : '-';
				},
				'filter' => \common\models\Sources::getList(true)
			],
            'dep_enable:booleanByLabel',
            'dep_default:booleanByLabel',
            'dep_updated_user_id:userName',
			[
				'attribute' => 'dep_updated_dt',
				'value' => static function (\common\models\DepartmentEmailProject $model) {
					return $model->dep_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dep_updated_dt)) : '-';
				},
				'format' => 'raw'
			],
        ],
    ]) ?>

</div>
