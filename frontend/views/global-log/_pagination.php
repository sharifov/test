<?php

/* @var $model common\models\search\GlobalLogSearch */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<nav>
    <ul class="pagination">
        <li class="page-item first <?= $model->reset ? null : 'disabled' ?>">
            <?= Html::a('Reset', [Url::toRoute(['global-log/index'])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item prev <?= (bool)$model->prevId ? null : 'disabled' ?>">
            <?= Html::a('Previous', [Url::current([
                'GlobalLogSearch[prevId]' => $model->prevId,
                'GlobalLogSearch[cursor]' => 1
            ])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item next <?= (bool) $model->nextId ? null : 'disabled' ?>">
            <?= Html::a('Next', [Url::current([
                'GlobalLogSearch[nextId]' => $model->nextId,
                'GlobalLogSearch[cursor]' => 2
            ])], ['class' => 'page-link']) ?>
        </li>
    </ul>
</nav>
