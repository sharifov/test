<?php

use sales\entities\cases\Cases;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\data\ActiveDataProvider $casesDataProvider */

?>

<?php Pjax::begin(['id' => 'pjax-client-cases', 'timeout' => 5000, 'enablePushState' => false]); ?>

    <?php echo GridView::widget([
        'dataProvider' => $casesDataProvider,
        'columns' => [
            [
                'attribute' => 'cs_id',
                'value' => static function (Cases $model) {
                    return Yii::$app->formatter->asCase($model, 'fa-cube');
                },
                'format' => 'raw',
                'header' => 'Case',
            ],
            [
                'attribute' => 'cs_created_dt',
                'value' => static function (Cases $model) {
                    return Yii::$app->formatter->asByUserDateTime($model->cs_created_dt);
                },
                'format' => 'raw',
                'header' => 'Created',
            ],
        ],
    ]) ?>

<?php Pjax::end() ?>
