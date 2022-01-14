<?php

use src\services\system\DbViewCryptService;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $tables [] */
/* @var $schema string */
/* @var array $viewCreateData */

$this->title = 'DB Schema "' . $schema . '"';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="db-info-page">
    <h1><?php echo  Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <h6>View List:</h6>
            <?php if ($tables) : ?>
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <th>Nr</th>
                        <th>Name</th>
                        <th>Create time</th>
                    </tr>
                <?php foreach ($tables as $n => $table) : ?>
                    <tr>
                        <td><?php echo($n + 1) ?></td>
                        <td>
                            <b><?php echo Html::encode($table['TABLE_NAME'])?></b>
                            <?php if (!DbViewCryptService::viewCheck($table['TABLE_NAME'], $viewCreateData)) : ?>
                                <i class="fa fa-exclamation-triangle text-danger" title="Not exist in VewCreateData"></i>
                            <?php endif ?>
                        </td>
                        <td><?php echo Html::encode($table['CREATE_TIME'])?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h6>Table and columns for encryption:</h6>
            <pre>
                <?php \yii\helpers\VarDumper::dump($viewCreateData, 10, true) ?>
            </pre>
        </div>
    </div>
</div>