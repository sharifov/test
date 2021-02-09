<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-4">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'project_key',
            'name:projectName',
            'link',
            'api_key',
            'email_postfix',
            'ga_tracking_id',
            //'contact_info:ntext',
            'closed:boolean',
            'p_update_user_id:userName',
            'last_update',
            'sort_order'
        ],
    ]) ?>



    </div>
    <div class="col-md-3 bg-white">
        <h2>Contact info:</h2>
        <?=\yii\helpers\VarDumper::dumpAsString($model->contactInfo->attributes, 10, true) ?>
    </div>
    <div class="col-md-5 bg-white">
        <h2>Parameters:</h2>
        <?=\yii\helpers\VarDumper::dumpAsString($model->p_params_json, 10, true) ?>
    </div>

</div>
