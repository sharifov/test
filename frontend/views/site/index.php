<?php

/* @var $this yii\web\View */

$this->title = 'Index';
?>
<div class="site-index">

    <div class="body-content">

        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Server Date Time</th>
                        <td><?= date('Y-m-d H:i:s')?></td>

                    </tr>
                    <tr>
                        <th>Local Date Time</th>
                        <td><?= Yii::$app->formatter->asDatetime(time())?></td>
                    </tr>
                </table>

            </div>

        </div>

    </div>
</div>
