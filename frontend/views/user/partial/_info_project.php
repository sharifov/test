<?php

use common\models\Employee;
use common\models\UserParams;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model Employee */

?>

<table class="data table table-striped no-margin">
    <thead>
    <tr>
        <th>#</th>
        <th>Project Name</th>
        <th>Client Company</th>
        <th class="hidden-phone">Hours Spent</th>
        <th>Contribution</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>1</td>
        <td>New Company Takeover Review</td>
        <td>Deveint Inc</td>
        <td class="hidden-phone">18</td>
        <td class="vertical-align-mid">
            <div class="progress">
                <div class="progress-bar progress-bar-success" data-transitiongoal="35" style="width: 35%;" aria-valuenow="35"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td>2</td>
        <td>New Partner Contracts Consultanci</td>
        <td>Deveint Inc</td>
        <td class="hidden-phone">13</td>
        <td class="vertical-align-mid">
            <div class="progress">
                <div class="progress-bar progress-bar-danger" data-transitiongoal="15" style="width: 15%;" aria-valuenow="15"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td>3</td>
        <td>Partners and Inverstors report</td>
        <td>Deveint Inc</td>
        <td class="hidden-phone">30</td>
        <td class="vertical-align-mid">
            <div class="progress">
                <div class="progress-bar progress-bar-success" data-transitiongoal="45" style="width: 45%;" aria-valuenow="45"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td>4</td>
        <td>New Company Takeover Review</td>
        <td>Deveint Inc</td>
        <td class="hidden-phone">28</td>
        <td class="vertical-align-mid">
            <div class="progress">
                <div class="progress-bar progress-bar-success" data-transitiongoal="75" style="width: 75%;" aria-valuenow="75"></div>
            </div>
        </td>
    </tr>
    </tbody>
</table>