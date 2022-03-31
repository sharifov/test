<?php

use modules\user\userFeedback\entity\UserFeedback;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedback */
/* @var $images modules\user\userFeedback\entity\UserFeedbackFile[] */

$this->title = $model->uf_id;
$this->params['breadcrumbs'][] = ['label' => 'User Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-feedback-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'uf_id',
                    'uf_title',
                    [
                        'attribute' => 'uf_type_id',
                        'value' => static function (UserFeedback $model) {
                            return $model->getTypeLabel();
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'uf_status_id',
                        'value' => static function (UserFeedback $model) {
                            return $model->getStatusLabel();
                        },
                        'format' => 'raw'
                    ],
                    'uf_message:ntext',
                    'uf_created_dt:byUserDateTime',
                    'uf_updated_dt:byUserDateTime',
                    'uf_created_user_id:username',
                    'uf_updated_user_id:username',
                    'uf_resolution:ntext',
                    'uf_resolution_user_id:username',
                    'uf_resolution_dt:byUserDateTime',
                ],
            ]) ?>
        </div>
        <div class="col-md-8">
            <?php if ($images) : ?>
                <h2>Screenshots:</h2>
                <div>
                    <?php foreach ($images as $image) : ?>
                        <img src="<?= $image->getImageSrc() ?>" alt="<?= $image->uff_title ?>" style="width: 100%;">
                    <?php endforeach; ?>
                </div>
            <?php endif;?>
        </div>
    </div>

</div>
