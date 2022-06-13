<?php

/* @var $model common\models\search\GlobalLogSearch */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<nav>
    <ul class="pagination">
        <li class="page-item first <?= $model->reset ? null : 'disabled' ?>">
            <?= Html::a('Reset', [Url::toRoute(['client-chat-request-crud/index'])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item prev <?= (bool)$model->prevId ? null : 'disabled' ?>">
            <?= Html::a('Previous', [Url::current([
                'ClientChatRequestSearch[prevId]' => $model->prevId,
                'ClientChatRequestSearch[cursor]' => 1
            ])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item next <?= (bool) $model->nextId ? null : 'disabled' ?>">
            <?= Html::a('Next', [Url::current([
                'ClientChatRequestSearch[nextId]' => $model->nextId,
                'ClientChatRequestSearch[cursor]' => 2
            ])], ['class' => 'page-link']) ?>
        </li>
    </ul>
</nav>
