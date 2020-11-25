<?php

/** @var array $report */
/** @var string $title */
/** @var array $backUrl */

$this->title = 'Client Chat Channels ' . $title;
$this->params['breadcrumbs'][] = $this->title;

use yii\bootstrap4\Html; ?>

<div class="client-chat-channel-report">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Back', $backUrl, ['class' => 'btn btn-default']) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <table class="table table-bordered table-hover">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Result</th>
                </tr>
                <?php foreach ($report as $item) : ?>
                    <tr>
                        <td>
                            <?= $item['id'] ?>
                        </td>
                        <td>
                            <?= $item['name'] ?>
                        </td>
                        <?php if ($item['message'] === 'Registered') : ?>
                        <td style="color: #28a048">
                        <?php else : ?>
                        <td style="color: #4f0800">
                        <?php endif; ?>
                            <?= $item['message'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
