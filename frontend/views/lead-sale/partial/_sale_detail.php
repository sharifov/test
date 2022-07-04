<?php

use yii\bootstrap4\Alert;
use yii\helpers\Html;

/**@var \src\model\sale\SaleDetail $sale */

if (isset($sale)) :?>
    <?php if (count($sale->processingTeamsStatus) > 0) : ?>
        <h2>Processing Teams Status</h2>
        <table class="table table-bordered table-hover">
            <tr>
                <th>Type</th>
                <th>Value</th>
            </tr>
            <?php foreach ($sale->processingTeamsStatus as $pStatusKey => $pStatusValue) : ?>
                <tr>
                    <td><?= Html::encode($pStatusKey) ?></td>
                    <td><?= Html::encode($pStatusValue) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>


    <div style="width: 100%;overflow-x: auto;">
        <?php if (count($sale->notes) > 0) : ?>
            <h2>Notes</h2>
            <table class="table table-bordered table-hover">
                <tr>
                    <th>Created</th>
                    <th>Message</th>
                    <th>Agent</th>
                    <th>Team</th>
                </tr>
                <?php foreach ($sale->notes as $note) : ?>
                    <tr>
                        <td><?= Yii::$app->formatter->asDatetime(strtotime($note['created'])) ?></td>
                        <td><?= Html::encode($note['message']) ?></td>
                        <td><?= Html::encode($note['agent']) ?></td>
                        <td><?= Html::encode($note['team']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>


    <?php if (count($sale->authList) > 0) : ?>
        <h2>Auth List</h2>
        <table class="table table-bordered table-hover table-striped">
            <tr>
                <th>Created</th>
                <th>Auth system</th>
                <th>For what</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Message</th>
                <th>CC Number</th>
            </tr>
            <?php foreach ($sale->authList as $list) : ?>
                <tr>
                    <td><?= Yii::$app->formatter->asDatetime(strtotime($list['created'])) ?></td>
                    <td><?= Html::encode($list['auth_system']) ?></td>
                    <td><?= Html::encode($list['for_what']) ?></td>
                    <td><?= number_format($list['amount'], 2) ?></td>
                    <td><?= Html::encode($list['status']) ?></td>
                    <td><?= Html::encode($list['message']) ?></td>
                    <td><?= Html::encode($list['ccNumber']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php else : ?>
    <div class="search-results__wrapper">
        <?php if (!empty($errorMessage)) : ?>
            <div class="row">
                <div class="col-md-12">
                    <?= Alert::widget([
                        'options' => [
                            'class' => 'alert-error',
                        ],
                        'body' => $errorMessage,
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
        <p>Sale Detail is empty</p>
    </div>

<?php endif ?>

