<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Notifications */

$this->title = $model->n_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('notifications', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('notifications', 'Update'), ['update', 'id' => $model->n_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('notifications', 'Delete'), ['delete', 'id' => $model->n_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('notifications', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
        <div class="col-md-6">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'n_id',
                    [
                        'attribute' => 'n_type_id',
                        'value' => function(\common\models\Notifications $model) {
                            return $model->getType();
                        }
                    ],
                    'n_title',
                    //'n_message:ntext',
                    //'n_new:boolean',

                    [
                        'attribute' => 'n_user_id',
                        'value' => function(\common\models\Notifications $model){
                            return $model->nUser->username;
                        },
                    ],

                    'n_new:boolean',
                    'n_deleted:boolean',
                    'n_popup:boolean',
                    'n_popup_show:boolean',

                    [
                        'attribute' => 'n_read_dt',
                        'value' => function (\common\models\Notifications $model) {
                            return $model->n_read_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_read_dt)) : '-';
                        },
                        'format' => 'raw'
                    ],

                    [
                        'attribute' => 'n_created_dt',
                        'value' => function (\common\models\Notifications $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_created_dt));
                        },
                        'format' => 'raw'
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <h4>Message text:</h4>
            <pre>
                <?php
                    echo $model->n_message;
                ?>
            </pre>
        </div>
    </div>
</div>
