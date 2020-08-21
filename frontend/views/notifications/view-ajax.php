<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Notifications */

$this->title = 'Notification - '. $model->n_title;

?>

<div class="notifications-view">

    <h1><i class="fa fa-bell-o"></i> <?= Html::encode($model->n_title) ?></h1>

    <div class="row">
        <div class="col-md-5">
            <pre><?php echo $model->n_title; ?></pre>
            <pre><?php echo Yii::$app->formatter->asNtextWithPurify($model->n_message); ?></pre>
        </div>
        <div class="col-md-7">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    //'n_title',
                    'n_id',
                    [
                      'attribute' => 'n_type_id',
                        'value' => function(\common\models\Notifications $model) {
                            return $model->getType();
                        }
                    ],

                    //'n_message:ntext',
                    //'n_new:boolean',
                    [
                        'attribute' => 'n_read_dt',
                        'value' => static function (\common\models\Notifications $model) {
                            return $model->n_read_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_read_dt)) : '-';
                        },
                        'format' => 'raw'
                    ],

                    [
                        'attribute' => 'n_created_dt',
                        'value' => static function (\common\models\Notifications $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_created_dt));
                        },
                        'format' => 'raw'
                    ],
                ],
            ]) ?>
        </div>

    </div>

</div>
