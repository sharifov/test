<?php


namespace console\controllers;

use ReflectionClass;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class LogController
 * @package console\controllers
 * @property bool $logCleanerEnable
 * @property array $logCleanerParams
 *
 * 
 */
class LogController extends Controller
{
    private $logCleanerEnable = true;
    private $logCleanerParams;

    /**
     * LogController constructor.
     * @param $id
     * @param $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
	{
		parent::__construct($id, $module, $config);

		$settings = Yii::$app->params['settings'];

		$this->logCleanerEnable = $settings['console_log_cleaner_enable'] ?? false;
		$this->logCleanerParams = json_decode($settings['console_log_cleaner_params'],true,512,JSON_THROW_ON_ERROR);
	}

	public function actionCleaner(int $days = 90, int $limit = 1000): void
	{
	    if (!$this->logCleanerEnable) {
            $this->printInfo('Cleaner is disable. ', $this->action->id, Console::FG_RED);
	        return;
	    }

	    $this->printInfo('Start. ',  $this->action->id);

        $this->cleanGlobalLog($days);


        $this->printInfo('End. ',  $this->action->id);

	}

	private function cleanGlobalLog(int $days, int $limit)
    {
        $days = $this->logCleanerParams['days'] ?
            (int) $this->logCleanerParams['days'] : $days;
        $limit = $this->logCleanerParams['limitIteration'] ?
            (int) $this->logCleanerParams['limitIteration'] : $limit;

        $prepareInfo = Yii::$app->db->createCommand(
        'SELECT 
                MAX(gl_id) AS max_id,    
                COUNT(gl_id) AS cnt
            FROM
                global_log
            WHERE
                gl_created_at < SUBDATE(CURDATE(), :days)'
        )->bindValue(':days', $days)->queryOne();
       
        $iterations =  (int)($prepareInfo['cnt'] / $limit);

		for ($i = 0; $i <= $iterations; $i++) {
			Yii::$app->db->createCommand(
			    'DELETE FROM global_log WHERE gl_id < :gl_id limit :limit')
			    ->bindValues([':gl_id' => $prepareInfo['max_id'], ':limit' => $limit])
			    ->execute();
		}

    }

    // Yii::info('CallId: ' . $this->c_id . ', '. VarDumper::dumpAsString($result) ,'info\checkCancelCall:cancelCall');

    /**
     * @param string $info
     * @param string $action
     * @param int $colorInfo
     * @param int $color
     */
    private function printInfo(string $info, string $action = '', $colorInfo = Console::FG_GREEN, $color = Console::FG_CYAN): void
    {
        try {
            $className = (new ReflectionClass(self::class))->getShortName();
        } catch (\Throwable $throwable) {
            $className = self::class;
        }

        printf(
            "\n --- %s %s --- \n",
            $this->ansiFormat($info, $colorInfo),
            $this->ansiFormat('(' . $className . ':' . $action . ')', $color)
        );
    }

}