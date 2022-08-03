<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $mailList [] */
/* @var $stats [] */


if (!$mailList || !is_array($mailList)) {
    $mailList = [];
}

?>
<div class="stats-body">

    <h1>Email Statistics</h1>
    <div class="row top_tiles">

        <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-envelope"></i></div>
                <div class="count">
                    <?= $stats['unread']?>
                </div>
                <h3>New Emails (unread)</h3>
                <p>Total new (unread) Email messages</p>
            </div>
        </div>

        <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-envelope-o"></i></div>
                <div class="count">
                    <?= $stats['inboxToday']?>
                </div>
                <h3>Today Inbox</h3>
                <p>Today inbox count of Email messages</p>
            </div>
        </div>

        <div class="animated flipInY col-md-2 col-sm-2 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-envelope-o"></i></div>
                <div class="count">
                    <?= $stats['outboxToday']?>
                </div>
                <h3>Today Outbox</h3>
                <p>Today outbox count of Email messages</p>
            </div>
        </div>

        <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-envelope-square"></i></div>
                <div class="count">
                    <?= $stats['draft']?>
                </div>
                <h3>Draft</h3>
                <p>Draft count of Email messages</p>
            </div>
        </div>

        <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-trash"></i></div>
                <div class="count">
                    <?= $stats['trash']?>
                </div>
                <h3>Trash</h3>
                <p>Trash count of Email messages</p>
            </div>
        </div>
    </div>


    <h2>My Email Accounts (<?=count($mailList)?>):</h2>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Nr</th>
            <th>Email</th>
        </tr>
        <?php
        $nr = 1;
        foreach ($mailList as $mail) :?>
        <tr>
            <td width="100px"><?=($nr++)?></td>
            <td><?=Html::encode($mail)?></td>
        </tr>
        <?php endforeach; ?>

    </table>

</div>