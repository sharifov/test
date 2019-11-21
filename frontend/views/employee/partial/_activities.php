<?php
/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $activity EmployeeActivity
 */

use yii\bootstrap\Html;
use common\models\Employee;
use common\models\EmployeeActivity;

?>


<?php if (true) :
    $pending = EmployeeActivity::getEmployeeLastActivity($model->id);
    ?>
    <div class="card card-default">
        <div class="panel-heading collapsing-heading">
            <?php $text = sprintf('Last Activities %s', !empty($pending) ? sprintf('(Active %s ago)', $pending) : ''); ?>
            <?= Html::a($text . ' <i class="collapsing-heading__arrow"></i>', '#activity-info', [
                'data-toggle' => 'collapse',
                'class' => 'collapsing-heading__collapse-link collapsed'
            ]) ?>
        </div>
        <div class="panel-body panel-collapse collapse" id="activity-info">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date/Time</th>
                    <th>IP Address</th>
                    <th>Type</th>
                    <th>Request</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (EmployeeActivity::getEmployeeActivity($model->id) as $key => $activity) : ?>
                    <tr>
                        <td><?= ($key + 1) ?></td>
                        <td><?= $activity->created ?></td>
                        <td><?= $activity->user_ip ?></td>
                        <td><?= $activity->request_type ?></td>
                        <td style="word-break: break-all;"><?= $activity->request ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
