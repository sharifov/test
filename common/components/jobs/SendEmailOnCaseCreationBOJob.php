<?php

namespace common\components\jobs;

use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Project;
use sales\dto\email\EmailConfigsDTO;
use sales\entities\cases\CaseCategory;
use sales\helpers\app\AppHelper;
use sales\repositories\cases\CasesRepository;
use sales\repositories\NotFoundException;
use sales\services\email\SendEmailByCase;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class SendEmailOnCaseCreationBOJob
 * @package common\components\jobs
 *
 * @property int $case_id
 * @property string $contact_email
 */
class SendEmailOnCaseCreationBOJob extends BaseJob implements JobInterface
{
    public $case_id;
    public $contact_email;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            (new SendEmailByCase($this->case_id, $this->contact_email));
        } catch (NotFoundException | \RuntimeException | \DomainException $e) {
            $message = AppHelper::throwableLog($e);
            $message['case_id'] = $this->case_id;
            \Yii::warning($message, 'SendEmailOnCaseCreationBOJob::Exception');
        } catch (\Throwable $e) {
            $message = AppHelper::throwableLog($e);
            $message['case_id'] = $this->case_id;
            \Yii::error($message, 'SendEmailOnCaseCreationBOJob::Throwable');
        }
    }
}
