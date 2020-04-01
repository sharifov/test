<?php


namespace console\controllers;

use common\bootstrap\Logger;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class LogController
 * @package console\controllers
 *
 */
class LogController extends Controller
{

    public function __construct($id, $module, $config = [])
	{
		parent::__construct($id, $module, $config);

	}

	public function actionCleaner($limit = 1000, $countDays = 90): void
	{

	}

}