<?php

use sales\model\clientChatRequest\entity\ClientChatRequest;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\data\ActiveDataProvider $dataProviderRequest */

?>

<?php Pjax::begin(['id' => 'pjax-browsing-history', 'timeout' => 5000, 'enablePushState' => false]); ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProviderRequest,
        'columns' => [
            [
                'attribute' => 'ccr_created_dt',
                'value' => static function (ClientChatRequest $model) {
                    return $model->ccr_created_dt ?
                        Yii::$app->formatter->asDatetime(strtotime($model->ccr_created_dt)) : '-';
                },
                'format' => 'raw',
                'header' => 'Created',
            ],
            [
                'label' => 'Url',
                'value' => static function (ClientChatRequest $model) {
                    if ($pageUrl = $model->getPageUrl()) {
                        return Yii::$app->formatter->asUrl($pageUrl);
                    }
                    return Yii::$app->formatter->nullDisplay;
                },
                'format' => 'raw',
                'header' => 'Url',
            ],
        ],
    ]) ?>

<?php Pjax::end();
