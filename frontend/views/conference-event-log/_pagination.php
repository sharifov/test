<?php

/* @var $model src\model\conference\entity\conferenceEventLog\search\ConferenceEventLogSearch */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<nav>
    <ul class="pagination">
        <li class="page-item first <?= $model->reset ? null : 'disabled' ?>">
            <?= Html::a('Reset', [Url::toRoute(['conference-event-log/index'])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item prev <?= (bool)$model->prevId ? null : 'disabled' ?>">
            <?= Html::a('Previous', [Url::current([
                'ConferenceEventLogSearch[prevId]' => $model->prevId,
                'ConferenceEventLogSearch[cursor]' => 1
            ])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item next <?= (bool) $model->nextId ? null : 'disabled' ?>">
            <?= Html::a('Next', [Url::current([
                'ConferenceEventLogSearch[nextId]' => $model->nextId,
                'ConferenceEventLogSearch[cursor]' => 2
            ])], ['class' => 'page-link']) ?>
        </li>
    </ul>
</nav>
