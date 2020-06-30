<?php
namespace console\controllers;

use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\services\log\GlobalLogFormatAttrService;
use yii\console\Controller;
use yii\helpers\Console;


/**
 * Class PostgresDbController
 * @package console\controllers
 *
 * @property GlobalLogFormatAttrService $globalLogFormatAttrService
 */
class PostgresDbController extends Controller
{
	/**
	 * @var GlobalLogFormatAttrService
	 */
    private GlobalLogFormatAttrService $globalLogFormatAttrService;

    /**
     * PostgresDbController constructor.
     * @param $id
     * @param $module
     * @param GlobalLogFormatAttrService $globalLogFormatAttrService
     * @param array $config
     */
    public function __construct($id, $module, GlobalLogFormatAttrService $globalLogFormatAttrService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->globalLogFormatAttrService = $globalLogFormatAttrService;
    }

    /**
     * Create next month partition for client_chat_message table
     * date_str force to create partition for provided month
     *
     * @param string|null $date format Y-m-d (ex. 2020-06-01)
     * @throws \Exception
     */
    public function actionCreateChatMessagePartition(string $date = null) : void
    {
        printf("\n %s\n", $this->ansiFormat('--- Start ' . $this->action->id . " ---", Console::FG_GREEN));
        printf("\n %s\n", $this->ansiFormat('date arg =  ' . $date , Console::FG_GREY));

        $start = date_create("now +1 month");
        if (!is_null($date)) {
            $start = date_create_from_format('Y-m-d', $date);
        }

        if (!$start) {
            printf("\n %s\n", $this->ansiFormat('invalid date arg =  ' . $date , Console::FG_RED));
            return;
        }

        $dates = ClientChatMessage::partitionDatesFrom($start);
        $start = $dates[0];
        $partitionEndDate =$dates[1];

        printf("\n %s\n", $this->ansiFormat('Partition from  ' . date_format($start, "Y-m-d").' TO '. date_format($partitionEndDate, "Y-m-d") , Console::FG_GREY));

        $tableName = ClientChatMessage::createMonthlyPartition($start, $partitionEndDate);
        printf("\n %s\n", $this->ansiFormat( 'Partition created ' . $tableName, Console::FG_GREY));
        printf("\n %s\n", $this->ansiFormat( '--- Done ' . $this->action->id . " ---", Console::FG_GREEN));
	}
}