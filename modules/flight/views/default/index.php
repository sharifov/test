<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var array $links */

$this->title = 'Hotel Product Module';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-default-index">
    <h1><i class="fa fa-hotel"></i> <?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-6">
            <?php if ($links): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nr</th>
                            <th>Name</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($links as $n => $link):?>
                        <tr>
                            <td><?=($n + 1)?></td>
                            <td><?=Html::encode($link['label'])?></td>
                            <td><?=\yii\bootstrap4\Html::a('Go to link', $link['url'], ['class' => 'btn btn-default'])?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
