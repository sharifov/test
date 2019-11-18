<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use sales\access\ListsAccess;

$this->title = 'Leads Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calls-report-index">
    <h1><i class=""></i> <?= Html::encode($this->title) ?></h1>

    <div class="">
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-search"></i> Search</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <?= $this->render('_search_leads_report' /*, ['model' => $searchModel, 'list' => $list]*/);  ?>
            </div>
        </div>
    </div>

    content

</div>