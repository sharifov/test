<?php

use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/** @var string $errorMessage */
/** @var int $chatId */
/** @var int $leadId */
/** @var ActiveDataProvider $dataProvider */

?>

<?php if ($errorMessage) : ?>
    <h3><?= $errorMessage ?></h3>
<?php else : ?>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,

        /*'options' => [
            'tag' => 'table',
            'class' => 'table table-bordered',
        ],*/
        'emptyText' => '<div class="text-center">Not found offers</div>',
        //'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_offer_item', ['offer' => $model, 'index' => $index]);
        },

        'itemOptions' => [
            //'class' => 'item',
            //'tag' => false,
        ],
    ]) ?>
    <?= Html::button('Send', ['class' => 'btn btn-info send-offer', 'data-chat-id' => $chatId, 'data-lead-id' => $leadId])?>
<?php endif; ?>
