<?php

use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/** @var string $errorMessage */
/** @var int $chatId */
/** @var int $leadId */
/** @var ActiveDataProvider $dataProvider */

?>
<?php if ($errorMessage): ?>
    <h3><?= $errorMessage ?></h3>
<?php else: ?>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_quote_item',
        'emptyText' => '<div class="text-center">Not found quotes</div><br>',
        //'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
        'itemOptions' => [
            //'class' => 'item',
            'tag' => false,
        ],
    ]);?>
    <?= Html::button('Generate', ['class' => 'btn btn-info quotes-uid-chat-generate', 'data-chat-id' => $chatId, 'data-lead-id' => $leadId])?>
<?php endif; ?>
