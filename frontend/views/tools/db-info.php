<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $tables [] */
/* @var $schema string */

$this->title = 'DB Info: "' . $schema . '"';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="db-info-page">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($tables): ?>
        <table class="table table-bordered table-hover table-striped">
            <tr>
                <th>Nr</th>
                <th>Name</th>
                <th>Engine</th>
                <th>Table Rows</th>
                <th>Data Length</th>
                <th>Index Length</th>
                <th>Auto increment</th>
                <th>Create time</th>
                <th>Table Collation</th>
            </tr>
        <?php foreach ($tables as $n => $table): ?>
            <tr>
                <td><?= ($n + 1) ?></td>
                <td><b><?=Html::encode($table['TABLE_NAME'])?></b></td>
                <td><?=Html::encode($table['ENGINE'])?></td>
                <td><?=Html::encode($table['TABLE_ROWS'])?></td>
                <td><?=Html::encode($table['DATA_LENGTH'])?></td>
                <td><?=Html::encode($table['INDEX_LENGTH'])?></td>
                <td><?=Html::encode($table['AUTO_INCREMENT'])?></td>
                <td><?=Html::encode($table['CREATE_TIME'])?></td>
                <td><?= $table['TABLE_COLLATION'] === 'utf8mb4_unicode_ci' ? Html::encode($table['TABLE_COLLATION']) : '<span class="danger">'.Html::encode($table['TABLE_COLLATION']) . '</span>'?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>