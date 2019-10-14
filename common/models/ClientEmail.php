<?php

namespace common\models;

use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "client_email".
 *
 * @property int $id
 * @property int $client_id
 * @property string $email
 * @property string $created
 * @property string $updated
 * @property string $comments
 * @property int $type
 *
 * @property Client $client
 */
class ClientEmail extends \yii\db\ActiveRecord implements AggregateRoot
{

    use EventTrait;

	public const EMAIL_TYPE = [
		1 => 'Valid',
		2 => 'Favorite',
		9 => 'Invalid'
	];

	public const EMAIL_TYPE_ICONS = [
		1 => '<i class="fa fa-check green"></i> ',
		2 => '<i class="fa fa-star yellow"></i> ',
		9 => '<i class="fa fa-close red"></i> '
	];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_email';
    }

	/**
	 * @param string $email
	 * @param int $clientId
	 * @param int $emailType
	 * @return static
	 */
    public static function create(string $email, int $clientId, int $emailType = null): self
    {
        $clientEmail = new static();
        $clientEmail->email = $email;
        $clientEmail->client_id = $clientId;
        $clientEmail->type = $emailType;
        return $clientEmail;
    }

	/**
	 * @param string $email
	 * @param int|null $emailType
	 */
	public function edit(string $email, int $emailType = null): void
	{
		$this->email = $email;
		$this->type = $emailType;
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['client_id', 'type'], 'integer'],
            [['created', 'updated', 'comments'], 'safe'],
            [['email'], 'string', 'max' => 100],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['email', 'client_id'], 'unique', 'targetAttribute' => ['email', 'client_id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'email' => 'Email',
            'created' => 'Created',
            'updated' => 'Updated',
			'type' => 'Email Type'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function beforeValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        if (strpos($this->email, 'wowfare') !== false) {
            $this->addError('email', 'Email is invalid!');
        }

        return parent::beforeValidate();
    }
}
