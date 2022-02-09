<?php

namespace frontend\models;

use common\models\Employee;
use common\models\Lead;
use src\model\leadUserRating\entity\LeadUserRating;
use yii\base\Model;

/**
 *
 */
class LeadUserRatingForm extends Model
{
    public $rating;

    public $leadId;

    public $userId;
    /**
     * @return array
     */

    public function __construct(Employee $user, $config = [])
    {
        $this->userId = $user->id;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['leadId','userId','rating'], 'required'],
            [['rating'], 'in', 'range' => array_values(LeadUserRating::RATING_LIST)],
            ['leadId', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id']],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
