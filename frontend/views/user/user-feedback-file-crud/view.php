<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedbackFile */

$this->title = $model->uff_id;
$this->params['breadcrumbs'][] = ['label' => 'User Feedback Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-feedback-file-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'uff_id' => $model->uff_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'uff_id' => $model->uff_id], [
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
            'uff_id',
            'uff_uf_id',
            'uff_mimetype',
            'uff_size',
            'uff_filename',
            'uff_title',
            'uff_blob',
            'uff_created_dt',
            'uff_created_user_id',
        ],
    ]) ?>

</div>
