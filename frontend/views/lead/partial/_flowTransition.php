<?php

/**
 * @var $flightRequestFlow LeadFlow[]
 * @var $this \yii\web\View
 */

use common\models\LeadFlow;

?>


<div class="sl-events-log">
    <table class="table table-neutral">
        <?php if (!empty($flightRequestFlow)) : ?>
            <thead>
            <tr>
                <th>Status</th>
                <th>Agent</th>
                <th>Created</th>
            </tr>
            </thead>
        <?php endif; ?>
        <tbody>
        <?php if (!empty($flightRequestFlow)) :
            foreach ($flightRequestFlow as $item) : ?>
                <tr>
                    <th><?= \common\models\Lead::getStatusLabel($item->status) ?></th>
                    <th><?= empty($item->employee) ? 'System' : $item->employee->username ?></th>
                    <th><?= $item->created ?></th>
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
