<?php

namespace sales\forms\api\clientEmail;


use yii\base\Model;

/**
 * Class UnSubscribeForm
 * @property string $email
 * @property int $project_id
 */
class UnSubscribeForm extends Model
{
    public string $email;
    public int $project_id;

    /**
     * SubscribeForm constructor.
     * @param int $project_id
     * @param array $config
     */
    public function __construct(int $project_id, $config = [])
    {
        parent::__construct($config);
        $this->project_id = $project_id;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'email_unsubscribe';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'email', 'max' => 160],

            [['email', 'project_id'], 'unique', 'targetAttribute' => ['eu_email', 'eu_project_id']],
        ];
    }
}
