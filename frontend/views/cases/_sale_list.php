<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CaseSaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-list"></i> Sale List</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?/*<li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#">Settings 1</a>
                </li>
                <li><a href="#">Settings 2</a>
                </li>
            </ul>
        </li>
        <li><a class="close-link"><i class="fa fa-close"></i></a>
        </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">
        <div class="case-sale-index">

            <table class="table table-bordered table-striped" style="padding: 10px; color: #0d3349;"><tr>
                    <td style="width: 11%">Sale ID</td>
                    <td style="width: 15%">BOOK Id</td>
                    <td style="width: 15%">PNR</td>
                    <td style="width: 10%">Pax</td>
                    <td>Sale Created Date</td>
                    <td>Added Date</td>
                </tr>
            </table>

            <?php Pjax::begin(['id' => 'pjax-sale-list']); ?>

            <?php

            $itemColls = [];
            if($items = $dataProvider->getModels()) {
                foreach ($items as $itemKey => $item) {

                    $label = '<table class="table table-bordered table-striped" style="margin: 0; color: #0d3349; font-size: 14px"><tr>
                        <td style="width: 10%">Id: '.Html::encode($item->css_sale_id).'</td>
                        <td style="width: 15%">'.Html::encode($item->css_sale_book_id).'</td>
                        <td style="width: 15%">'.Html::encode($item->css_sale_pnr).'</td>
                        <td style="width: 10%">'.Html::encode($item->css_sale_pax).'</td>
                        <td>'.Yii::$app->formatter->asDatetime($item->css_sale_created_dt).'</td>
                        <td>'.Yii::$app->formatter->asDatetime($item->css_created_dt).'</td>
                    </tr></table>';

                    $content = '';

                    $dataSale = @json_decode($item->css_sale_data, true);
                    if(is_array($dataSale)) {
                        $content = $this->renderAjax('/sale/view', ['data' => $dataSale]);
                    }



                    $itemColls[] = [
                        'label' => $label,
                        'content' => $content,
                        'contentOptions' => ['class' => $itemKey ? '' : 'in']
                        //'options' => [...],
                        //'footer' => 'Footer' // the footer label in list-group
                    ];
                }
            }

            echo \yii\bootstrap\Collapse::widget([
                'encodeLabels' => false,
                'items' => $itemColls
            ]);

            ?>


            <?/*= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],

                    //'css_cs_id',
                    'css_sale_id',
                    'css_sale_book_id',
                    'css_sale_pnr',
                    'css_sale_pax',
                    'css_sale_created_dt',
                    //'css_sale_data',
                    // 'css_created_user_id',
                    // 'css_updated_user_id',
                    'css_created_dt',
                    'css_updated_dt',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);*/ ?>

            <?php Pjax::end(); ?>

        </div>
    </div>
</div>
