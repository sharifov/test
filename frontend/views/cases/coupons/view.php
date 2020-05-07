<?php

?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-case-coupons', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel">
	<div class="x_title" >
		<h2><i class="fa fa-sticky-note-o"></i> Coupons </h2>
		<ul class="nav navbar-right panel_toolbox">
			<li>
			</li>
			<li>
				<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
	<div class="x_content" style="display: none; margin-top: -10px;">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr>
                <th style="width: 50px">#</th>
                <th style="width: 30px"> </th>
                <th class="text-center" style="width: 130px">Code</th>
                <th class="text-center" style="width: 130px">Amount</th>
                <th class="text-center" style="width: 130px">Currency Code</th>
                <th class="text-center" style="width: 130px">Percent</th>
                <th class="text-center" style="width: 130px">Exp Date</th>
                <th class="text-center" style="width: 130px">Start Date</th>
                <th class="text-center" style="width: 130px">Status</th>
                <th class="text-center">Notes</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="10" style="text-align: center">Not Found Data</th>
                </tr>
            </tbody>
        </table>
	</div>
</div>
<?php \yii\widgets\Pjax::end()?>
