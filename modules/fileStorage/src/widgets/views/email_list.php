<?php

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var array $files */
/** @var string $checkBoxName */

?>

<div class="x_title">
    <h2>Files</h2>
    <div class="clearfix"></div>
</div>
<div class="x_content" >
    <table class="table table-bordered">
        <tr>
            <td style="width: 40px"></td>
            <td>Filename</td>
            <td>Title</td>
        </tr>
        <?php foreach ($files as $file) : ?>
            <tr>
                <td><input type="checkbox" name="<?= $checkBoxName ?>[files][]" value="<?= $file['id'] ?>" /></td>
                <td><?= Html::encode($file['name']) ?></td>
                <td><?= Html::encode($file['title']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
