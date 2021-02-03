<?php

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var array $files */

?>

<div class="x_title">
    <h2>Files</h2>
    <div class="clearfix"></div>
</div>
<div class="x_content" >
    <table class="table table-bordered" style="font-size: 14px">
        <tr>
            <td>Filename</td>
            <td>Title</td>
        </tr>
        <?php foreach ($files as $file) : ?>
            <tr>
                <td><?= Html::encode(($file['name'] ?? '')) ?></td>
                <td><?= Html::encode(($file['title'] ?? '')) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
