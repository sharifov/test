<?php

namespace frontend\widgets\redial;

use common\models\Call;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use sales\model\leadRedial\services\ClientPhones;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\FindPhoneParams;
use sales\services\lead\qcall\QCallService;
use Yii;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\Project;
use yii\base\Widget;

/**
 * Class LeadRedialWidget
 *
 * @property Lead $lead
 * @property RedialUrl $viewUrl
 * @property RedialUrl $takeUrl
 * @property RedialUrl $reservationUrl
 * @property RedialUrl $phoneNumberFromUrl
 * @property RedialUrl $checkBlackPhoneUrl
 * @property string $script
 * @property ClientPhonesDTO[] $phonesTo
 * @property ClientPhones $clientPhones
 */
class LeadRedialWidget extends Widget
{

    public $lead;

    public $viewUrl;

    public $takeUrl;

    public $reservationUrl;

    public $phoneNumberFromUrl;

    public $checkBlackPhoneUrl;

    public $script;

    public $phonesTo;

    private $clientPhones;

    public function init(): void
    {
        parent::init();
        if (!$this->lead instanceof Lead) {
            throw new \InvalidArgumentException('lead property must be Lead');
        }
        if (!$this->viewUrl instanceof RedialUrl) {
            throw new \InvalidArgumentException('viewUrl property must be RedialUrl');
        }
        if (!$this->takeUrl instanceof RedialUrl) {
            throw new \InvalidArgumentException('takeUrl property must be RedialUrl');
        }
        if (!$this->reservationUrl instanceof RedialUrl) {
            throw new \InvalidArgumentException('reservationUrl property must be RedialUrl');
        }
        if (!$this->phoneNumberFromUrl instanceof RedialUrl) {
            throw new \InvalidArgumentException('phoneNumberFromUrl property must be RedialUrl');
        }
        if (!$this->checkBlackPhoneUrl instanceof RedialUrl) {
            throw new \InvalidArgumentException('checkBlackPhoneUrl property must be RedialUrl');
        }
        $this->clientPhones = Yii::createObject(ClientPhones::class);
    }

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('lead_redial', [
            'lead' => $this->lead,
            'viewUrl' => $this->viewUrl,
            'takeUrl' => $this->takeUrl,
            'phoneNumberFromUrl' => $this->phoneNumberFromUrl,
            'checkBlackPhoneUrl' => $this->checkBlackPhoneUrl,
            'reservationUrl' => $this->reservationUrl,
            'script' => $this->script,
            'phonesTo' => $this->findPhonesTo(),
            'projectId' => $this->findProjectId(),
            'redialAutoTakeSeconds' => $this->findRedialAutoTakeSeconds(),
        ]);
    }

    /**
     * @return int
     */
    private function findRedialAutoTakeSeconds(): int
    {
        return Yii::$app->params['settings']['redial_auto_take_seconds'] ?? 10;
    }

    /**
     * @return ClientPhonesDTO[]
     */
    private function findPhonesTo(): array
    {
        if ($this->phonesTo) {
            return $this->phonesTo;
        }

        $phones = $this->clientPhones->getPhones($this->lead);

        if ($phones) {
            return $phones;
        }

        throw new \DomainException('Not found phonesTo for LeadId: ' . $this->lead->id);
    }

    /**
     * @return int
     */
    private function findProjectId(): int
    {
        if ($this->lead->project_id) {
            return $this->lead->project_id;
        }
        throw new \DomainException('Not found projectId for LeadId: ' . $this->lead->id);
    }
}
