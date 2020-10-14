<?php

use common\models\Lead;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\data\ActiveDataProvider $leadDataProvider */

?>

<?php Pjax::begin(['id' => 'pjax-client-leads', 'timeout' => 5000, 'enablePushState' => false]); ?>

    <?php echo GridView::widget([
        'dataProvider' => $leadDataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'value' => static function (Lead $model) {
                    return Yii::$app->formatter->asLead($model, 'fa-cubes');
                },
                'format' => 'raw',
                'header' => 'Lead',
            ],
            [
                'attribute' => 'created',
                'value' => static function (Lead $model) {
                    return Yii::$app->formatter->asByUserDateTime($model->created);
                },
                'format' => 'raw',
                'header' => 'Created',
            ],
        ],
    ]) ?>

<?php Pjax::end() ?>
