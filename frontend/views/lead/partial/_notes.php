<?php
/**
 * @var $this \yii\web\View
 * @var $notes \common\models\Note[]
 */

?>

<div class="panel panel-neutral panel-wrapper agents-notes-block">
    <div class="panel-heading collapsing-heading">
        <a data-toggle="collapse" href="#agents-notes" aria-expanded="true"
           class="collapsing-heading__collapse-link">
            Agents Notes
            <i class="collapsing-heading__arrow"></i>
        </a>
    </div>
    <div class="collapse in" id="agents-notes" aria-expanded="true" style="">
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-neutral table-striped">
                    <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Date Time</th>
                        <th>Note</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notes as $note) : ?>
                        <tr>
                            <td><i class="fa fa-user"></i> <?= $note->employee->username ?> (<?= $note->employee->id ?>)</td>
                            <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($note->created), 'php: Y-m-d [H:i]') ?></td>
                            <td><?= $note->message ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>