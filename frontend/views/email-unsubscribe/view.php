<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use sales\helpers\email\MaskEmailHelper;

/* @var $this yii\web\View */
/* @var $model common\models\EmailUnsubscribe */

$this->title = MaskEmailHelper::masking($model->eu_email);
$this->params['breadcrumbs'][] = ['label' => 'Email Unsubscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="email-unsubscribe-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'eu_email' => $model->eu_email, 'eu_project_id' => $model->eu_project_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'eu_email' => $model->eu_email, 'eu_project_id' => $model->eu_project_id], [
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
            //'eu_email:email',
            [
                'attribute' => 'eu_email',
                'value' => static function (\common\models\EmailUnsubscribe $model) {
                    return MaskEmailHelper::masking($model->eu_email);
                },
                'format' => 'email'
            ],
            'eu_project_id',
            'eu_created_user_id',
            'eu_created_dt',
        ],
    ]) ?>

</div>
