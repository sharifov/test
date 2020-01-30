<?php

namespace modules\flight\models;

use common\models\Employee;
use sales\entities\EventTrait;
use Yii;
use yii\base\InvalidArgumentException;

/**
 * This is the model class for table "flight_quote_status_log".
 *
 * @property int $qsl_id
 * @property int|null $qsl_created_user_id
 * @property int $qsl_flight_quote_id
 * @property int|null $qsl_status_id
 * @property string|null $qsl_created_dt
 *
 * @property Employee $qslCreatedUser
 * @property FlightQuote $qslFlightQuote
 */
class FlightQuoteStatusLog extends \yii\db\ActiveRecord
{
	use EventTrait;

	public const STATUS_CREATED = 1;
	public const STATUS_APPLIED = 2;
	public const STATUS_DECLINED = 3;
	public const STATUS_SEND = 4;
	public const STATUS_OPENED = 5;


	public CONST STATUS_LIST = [
		self::STATUS_CREATED => 'New',
		self::STATUS_APPLIED => 'Applied',
		self::STATUS_DECLINED => 'Declined',
		self::STATUS_SEND => 'Sent',
		self::STATUS_OPENED => 'Opened'
	];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'flight_quote_status_log';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['qsl_created_user_id', 'qsl_flight_quote_id', 'qsl_status_id'], 'integer'],
			[['qsl_flight_quote_id'], 'required'],
			[['qsl_created_dt'], 'safe'],
			[['qsl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qsl_created_user_id' => 'id']],
			[['qsl_flight_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['qsl_flight_quote_id' => 'fq_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'qsl_id' => 'Qsl ID',
			'qsl_created_user_id' => 'Qsl Created User ID',
			'qsl_flight_quote_id' => 'Qsl Flight Quote ID',
			'qsl_status_id' => 'Qsl Status ID',
			'qsl_created_dt' => 'Qsl Created Dt',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getQslCreatedUser()
	{
		return $this->hasOne(Employee::class, ['id' => 'qsl_created_user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getQslFlightQuote()
	{
		return $this->hasOne(FlightQuote::class, ['fq_id' => 'qsl_flight_quote_id']);
	}

	/**
	 * {@inheritdoc}
	 * @return \modules\flight\models\query\FlightQuoteStatusLogQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \modules\flight\models\query\FlightQuoteStatusLogQuery(static::class);
	}

	/**
	 * @return bool
	 */
	public function isApplied(): bool
	{
		return $this->qsl_status_id === self::STATUS_APPLIED;
	}

	/**
	 * @return void
	 */
	public function decline(): void
	{
		$this->setStatus(self::STATUS_DECLINED);
	}

	/**
	 * @param int $status
	 */
	private function setStatus(int $status): void
	{
		if (!array_key_exists($status, self::STATUS_LIST)) {
			throw new InvalidArgumentException('Invalid Status');
		}
		$this->qsl_status_id = $status;
	}

	/**
	 * @param int $userId
	 * @param int $flightQuoteId
	 * @param int $statusId
	 * @return FlightQuoteStatusLog
	 */
	public static function create(int $userId, int $flightQuoteId, int $statusId): FlightQuoteStatusLog
	{
		$log = new self();
		$log->qsl_created_user_id = $userId;
		$log->qsl_flight_quote_id = $flightQuoteId;
		$log->qsl_status_id = $statusId;
		$log->qsl_created_dt = date('Y-m-d H:i:s');
		return $log;
	}

	/**
	 * @param int $statusId
	 * @return mixed|string
	 */
	public static function getStatusText(int $statusId)
	{
		return self::STATUS_LIST[$statusId] ?? '';
	}
}
