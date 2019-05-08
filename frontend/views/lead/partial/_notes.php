<?php
/**
 * @var $this \yii\web\View
 * @var $notes \common\models\Note[]
 */

?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-th-list"></i> Agent Notes (<?=count($notes)?>)</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                &nbsp;
            </li>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>

            <?/*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-comment"></i></a>


                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: none;">
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
                            <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($note->created)) ?></td>
                            <td><?= $note->message ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
    </div>
</div>