<?php
namespace sales\model\lead\useCases\lead\create;

use common\models\Department;
use common\models\Lead;
use common\models\Sources;
use sales\access\EmployeeProjectAccess;
use sales\access\ListsAccess;
use sales\forms\CompositeForm;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\forms\lead\PreferencesCreateForm;
use sales\helpers\lead\LeadHelper;
use sales\repositories\cases\CasesRepository;
use sales\repositories\NotFoundException;

/**
 * Class LeadCreateForm2
 * @package sales\forms\lead
 *
 * @property integer $source
 * @property integer $projectId
 * @property string $clientPhone
 * @property string $clientEmail
 * @property string $status
 * @property string $requestIp;
 * @property string $caseGid
 * @property int $depId
 * @property int|null $userId
 * @property ClientCreateForm $client
 * @property EmailCreateForm[] $emails
 * @property PhoneCreateForm[] $phones
 * @property PreferencesCreateForm $preferences
 */
class LeadManageForm extends CompositeForm
{
	public $source;
	public $projectId;
	public $clientPhone;
	public $clientEmail;
	public $status;
	public $caseGid;
	public $depId;
	public $requestIp;

	private $userId;

	/**
	 * LeadCreateForm constructor.
	 * @param int $countEmails
	 * @param int $countPhones
	 * @param int|null $userId
	 * @param array $config
	 */
	public function __construct(int $countEmails = 1, int $countPhones = 1, ?int $userId = null, $config = [])
	{
		$this->status = Lead::STATUS_PROCESSING;

		$this->client = new ClientCreateForm();

		$this->emails = array_map(static function () {
			return new EmailCreateForm();
		}, self::createCountMultiField($countEmails));

		$this->phones = array_map(static function () {
			return new PhoneCreateForm();
		}, self::createCountMultiField($countPhones));

		$this->preferences = new PreferencesCreateForm();

		$this->userId = $userId;

		parent::__construct($config);
	}

	/**
	 * @return array
	 */
	public function rules(): array
	{
		return [

			['source', 'required'],
			['source', 'integer'],
			['source', 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['source' => 'id']],
			['source', function () {
				if ($projectId = Sources::find()->select('project_id')->where(['id' => $this->source])->asArray()->limit(1)->one()) {
					$this->projectId = $projectId['project_id'];
					if (!EmployeeProjectAccess::isInProject($this->projectId, $this->userId)) {
						$this->addError('source', 'Access denied for this project');
					}
				} else {
					$this->addError('source', 'Project not found');
				}
				if ($source = Sources::findOne($this->source)) {
					$this->validateRequiredRules($source->rule);
				} else {
					$this->addError('source', 'Not found Source rules');
				}
			}],

			['requestIp', 'ip'],

			[['emails', 'phones'], 'safe'],

			['status', 'in', 'range' => array_keys(LeadHelper::statusList())],

			['caseGid', 'string'],
			['caseGid', function () {
				if (!$this->caseGid) {
					return;
				}
				$casesRepository = \Yii::createObject(CasesRepository::class);
				try {
					$case = $casesRepository->findFreeByGid((string)$this->caseGid);
				} catch (NotFoundException $e) {
					$this->addError('caseGid', 'Case not found');
				} catch (\DomainException $e) {
					$this->addError('caseGid', 'Case is already assigned to Lead');
				}
			}],

			['depId', 'required'],
			['depId', 'in', 'range' => [Department::DEPARTMENT_SALES, Department::DEPARTMENT_EXCHANGE]],
		];
	}

	public function validateRequiredRules($ruleId): void
	{
		switch ($ruleId) {
			case Sources::RULE_EMAIL_REQUIRED:
				$this->sourceRulesEmailRequired();
				break;
			case Sources::RULE_EMAIL_OR_PHONE_REQUIRED:
				$this->sourceRulesEmailOrPhoneRequired();
				break;
			case Sources::RULE_EMAIL_AND_PHONE_REQUIRED:
				$this->sourceRulesEmailAndPhoneRequired();
				break;
			case Sources::RULE_PHONE_REQUIRED:
			default:
				$this->sourceRulesPhoneRequired();
		}
	}

	private function sourceRulesPhoneRequired(): void
	{
		if (count($this->phones) < 1) {
			$this->addError('source', 'Phone cannot be blank.');
			return;
		}
		foreach ($this->phones as $phone) {
			$phone->required = true;
		}
	}

	private function sourceRulesEmailRequired(): void
	{
		if (count($this->emails) < 1) {
			$this->addError('source', 'Email cannot be blank.');
			return;
		}
		foreach ($this->emails as $email) {
			$email->required = true;
		}
	}

	private function sourceRulesEmailAndPhoneRequired(): void
	{
		if (count($this->emails) < 1) {
			$this->addError('source', 'Email cannot be blank.');
			return;
		}
		if (count($this->phones) < 1) {
			$this->addError('source', 'Phone cannot be blank.');
			return;
		}
		foreach ($this->emails as $email) {
			$email->required = true;
		}
		foreach ($this->phones as $phone) {
			$phone->required = true;
		}
	}

	private function sourceRulesEmailOrPhoneRequired(): void
	{
		if (count($this->emails) < 1 && count($this->phones) < 1) {
			$this->addError('source', 'Email or Phone cannot be blank.');
			return;
		}

		$oneEmailIsNotEmpty = false;
		$onePhoneIsNotEmpty = false;

		foreach ($this->emails as $email) {
			if ($email->email) {
				$oneEmailIsNotEmpty = true;
			}
		}
		foreach ($this->phones as $phone) {
			if ($phone->phone) {
				$onePhoneIsNotEmpty = true;
			}
		}

		if (!$oneEmailIsNotEmpty && !$onePhoneIsNotEmpty) {
			foreach ($this->emails as $email) {
				$email->required = true;
				$email->message = 'Email or Phone cannot be blank.';
			}
			foreach ($this->phones as $phone) {
				$phone->required = true;
				$phone->message = 'Phone or Email cannot be blank.';
			}
		} else {
			foreach ($this->emails as $email) {
				$email->required = false;
			}
			foreach ($this->phones as $phone) {
				$phone->required = false;
			}
		}
	}

	public function validate($attributeNames = null, $clearErrors = true): bool
	{
		if (parent::validate($attributeNames, $clearErrors)) {
			$this->loadClientData();
			$this->checkEmptyPhones();
			$this->checkEmptyEmails();
			return true;
		}
		$this->checkEmptyPhones();
		$this->checkEmptyEmails();
		return false;
	}

	private function loadClientData(): void
	{

		if (!$this->hasErrors()) {
			if (isset($this->emails[0]) && $this->emails[0]->email) {
				$this->clientEmail = $this->emails[0]->email;
			} else {
				$this->clientEmail = '';
			}
			if (isset($this->phones[0]) && $this->phones[0]->phone) {
				$this->clientPhone = $this->phones[0]->phone;
			} else {
				$this->clientPhone = '';
			}
		}
	}

	private function checkEmptyPhones(): void
	{
		$errors = false;
		foreach ($this->phones as $key => $phone) {
			if ($key > 0 && !$phone->phone) {
				if (!$this->getErrors('phones.' . $key . '.phone')) {
					$errors = true;
					$this->addError('phones.' . $key . '.phone', 'Phone cannot be blank.');
				}
			}
		}
		if (!$errors && count($this->phones) > 1 && isset($this->phones[0]->phone) && !$this->phones[0]->phone) {
			if (!$this->getErrors('phones.0.phone')) {
				$this->addError('phones.0.phone', 'Phone cannot be blank.');
			}
		}
	}

	private function checkEmptyEmails(): void
	{
		$errors = false;
		foreach ($this->emails as $key => $email) {
			if ($key > 0 && !$email->email) {
				if (!$this->getErrors('emails.' . $key . '.email')) {
					$errors = true;
					$this->addError('emails.' . $key . '.email', 'Email cannot be blank.');
				}
			}
		}
		if (!$errors && count($this->emails) > 1 && isset($this->emails[0]->email) && !$this->emails[0]->email) {
			if (!$this->getErrors('emails.0.email')) {
				$this->addError('emails.0.email', 'Email cannot be blank.');
			}
		}
	}

	/**
	 * @param int $depId
	 */
	public function assignDep(int $depId): void
	{
		$this->depId = $depId;
	}

	/**
	 * @return array
	 */
	public function listSources(): array
	{
		return (new ListsAccess($this->userId))->getSources();
	}

	/**
	 * @return array
	 */
	public function internalForms(): array
	{
		return ['client', 'emails', 'phones', 'preferences'];
	}

	/**
	 * @return array
	 */
	public function attributeLabels(): array
	{
		return [
			'requestIp' => 'Client IP',
			'source' => 'Marketing Info:',
		];
	}
}