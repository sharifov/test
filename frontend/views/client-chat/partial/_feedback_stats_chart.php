<?php
use yii\grid\GridView;

/**
 * @var $viewModel \sales\viewModel\chat\ViewModelChatFeedbackGraph
 */

?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php if (true): ?>

<?= GridView::widget([
    'dataProvider' => $viewModel->dataProvider,
    'columns' => [
        [
            'label' => 'Client',
            'value' => function($model){
                return $model['first_name'] .' '. $model['last_name'] ;
            }
        ],

        [
            'label' => 'Agent',
            'attribute' => 'username'
        ],
        [
            'label' => 'Rating',
            'attribute' => 'ccf_rating'
        ],
        [
            'label' => 'Remark',
            'attribute' => 'ccf_message'
        ]
    ],
])?>

<?php else: ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <p style="margin: 0;">Not Found Data</p>
        </div>
    </div>
<?php endif; ?>
