<?php

/**
 * @var $data QuoteStatusLog[]
 * @var $this \yii\web\View
 */

use common\models\QuoteStatusLog;

?>


<div class="sl-events-log">
    <table class="table table-neutral">
        <?php if (!empty($data)) : ?>
            <thead>
            <tr>
                <th>Status</th>
                <th>Agent</th>
                <th>Created</th>
            </tr>
            </thead>
        <?php endif; ?>
        <tbody>
        <?php if (!empty($data)) :
        foreach ($data as $item) : ?>
                <tr>
                    <th><?= \common\models\Quote::getLabelByStatus($item->status) ?></th>
                    <th><?= empty($item->employee) ? 'System' : $item->employee->username ?></th>
                    <th><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($item->created)) ?></th>
                </tr>
            <?php endforeach;
        else : ?>
            <tr>
                <th class="text-bold text-center">Not found info for this request!</th>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
