<?php
namespace sales\model\coupon\useCase\send;

use common\models\EmailTemplateType;
use sales\entities\cases\Cases;
use sales\model\coupon\entity\coupon\Coupon;
use yii\base\Model;

/**
 * Class SendCouponsForm
 * @package sales\model\coupon\useCase\send
 *
 * @var int $caseId
 * @var array $couponIds
 * @var string $emailTemplateType
 * @var string $emailTo
 */
class SendCouponsForm extends Model
{
	public $caseId;
	public $couponIds = [];
	public $emailTemplateType;
	public $emailTo;

	public function __construct(int $caseId = null, $config = [])
	{
		parent::__construct($config);
		$this->caseId = $caseId;
	}

	public function rules()
	{
		return [
			['caseId', 'required'],
			['caseId', 'exist', 'targetAttribute' => 'cs_id', 'targetClass' => Cases::class],

			['couponIds', 'required'],
			['couponIds', 'each', 'rule' => ['exist', 'targetAttribute' => 'c_id', 'targetClass' => Coupon::class ]],

			['emailTemplateType', 'required'],
			['emailTemplateType', 'exist', 'targetAttribute' => 'etp_key', 'targetClass' => EmailTemplateType::class],

			['emailTo', 'required'],
		];
	}


}