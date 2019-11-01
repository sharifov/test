<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GlobalLog */

$this->title = $model->gl_id;
$this->params['breadcrumbs'][] = ['label' => 'Global Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="global-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'gl_id',
            'gl_app_id',
            'gl_app_user_id',
            'gl_model',
            'gl_obj_id',
            'gl_old_attr',
            'gl_new_attr',
            'gl_formatted_attr',
            'gl_created_at',
        ],
    ]) ?>

</div>
