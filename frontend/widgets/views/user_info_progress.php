<?php

use common\models\Employee;

/** @var Employee $user */
/** @var string $completedTasksPercent */
/** @var string $newLeadsCount */

?>

<div class="col-md-3">
    <table class="table table-bordered">
        <?php /*<tr>
                    <th>Taked New Leads current shift</th>
                    <td><?=$user->getCountNewLeadCurrentShift()?></td>
                </tr>*/ ?>
        <tr>
            <th>Minimal percent for take new lead</th>
            <td><?= $user->userParams->up_min_percent_for_take_leads ?>%</td>
        </tr>
        <tr>
            <th>Default limit for take new lead</th>
            <td><?= $user->userParams->up_default_take_limit_leads ?></td>
        </tr>
        <tr>
            <th>Current Shift task progress</th>
            <td style="width: 50%">
                <div class="progress" title="<?= $completedTasksPercent ?>%">
                    <div class="progress-bar" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0"
                         aria-valuemax="100" style="width: <?= $completedTasksPercent ?>%;">
                        <?= $completedTasksPercent ?>%
                    </div>
                </div>
            </td>
        </tr>

    </table>
</div>

<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="tile-stats">
        <div class="icon"><i class="fa fa-newspaper-o"></i>
        </div>
        <div class="count"><?= $newLeadsCount ?></div>

        <h3>Taked New Leads</h3>
        <p>Current shift</p>
    </div>
</div>
