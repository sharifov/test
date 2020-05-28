<?php
namespace common\models;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\AttributeBehavior;

/**
 * Airline model
 *
 * @property integer $id
 * @property string $name
 * @property string $iata
 * @property string $code
 * @property string $iaco
 * @property string $countryCode
 * @property string $country
 * @property string $cl_economy
 * @property string $cl_premium_economy
 * @property string $cl_business
 * @property string $cl_premium_business
 * @property string $cl_first
 * @property string $cl_premium_first
 * @property string $updated_dt
 */
class Airline extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%airlines}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($iata)
    {
        return static::findOne(['iata' => $iata]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'iata'], 'required'],
            [['code', 'iaco', 'countryCode', 'country','cl_economy', 'cl_premium_economy', 'cl_business', 'cl_premium_business', 'cl_first', 'cl_premium_first'], 'safe'],
            [['iata'], 'string', 'max' => 2],
            [['updated_dt'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'updated_dt'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_dt'
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public static function getAirlinesMapping($fullName = false)
    {
        return ($fullName)
            ? ArrayHelper::map(self::find()->asArray()->all(), 'iata', 'name')
            : ArrayHelper::map(self::find()->asArray()->all(), 'iata', 'iata');
    }

    public function getCabinByClass($class)
    {
        if(in_array($class, explode(',', $this->cl_economy))){
            return 'E';
        }elseif(in_array($class, explode(',', $this->cl_business))){
            return 'B';
        }elseif(in_array($class, explode(',', $this->cl_first))){
            return 'F';
        }elseif(in_array($class, explode(',', $this->cl_premium_economy))){
            return 'P';
        }elseif(in_array($class, explode(',', $this->cl_premium_business))){
            return 'PB';
        }elseif(in_array($class, explode(',', $this->cl_premium_first))){
            return 'PF';
        }

        return null;
    }

    /**
     * @throws \yii\httpclient\Exception
     */
    public static function syncCabinClasses(): void
    {
        $flgSync = true;
        $response = \Yii::$app->airsearch->sendRequest('airline/get-cabin-classes', [], 'GET');

        if ($response->isOk) {
            $headers = $response->getHeaders()->toArray();
            if ($lastModifiedHeader = $headers['last-modified'][0]) {
                $lastModified = new \DateTime($lastModifiedHeader);
                $lastUpdated = self::find()
                    ->where(['NOT', ['updated_dt' => null]])
                    ->orderBy(['updated_dt'  => SORT_DESC ])
                    ->limit(1)
                    ->asArray()
                    ->all();

                if(!empty($lastUpdated)){
                    if(new \DateTime($lastUpdated[0]['updated_dt']) >= $lastModified){
                        $flgSync = false;
                    }
                }
            }

            if($flgSync){
                $content = $response->getContent();
                if(!empty($content)){
                    try {
                        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                        if(isset($data['results'])){
                            foreach ($data['results'] as $entry){
                                $airline = Airline::findOne(['iata'=> $entry['airlineCode']]);
                                if($airline === null){
                                    $airline = new Airline();
                                    $airline->iata = $entry['airlineCode'];
                                }
                                $airline->cl_economy = $entry['economy'];
                                $airline->cl_premium_economy = $entry['premium-economy'];
                                $airline->cl_business = $entry['business'];
                                $airline->cl_premium_business = $entry['premium-business'];
                                $airline->cl_first = $entry['first'];
                                $airline->cl_premium_first = $entry['premium-first'];
                                $airline->name = $entry['airlineName'];
                                if (!$airline->save()) {
                                    \Yii::error(
                                        \yii\helpers\VarDumper::dumpAsString([$entry, $airline->getErrors()], 20),
                                        'Airline:syncCabinClasses:notSaved'
                                    );
                                }
                            }
                        }
                    } catch (\JsonException $e) {
                        \Yii::error(
                            \yii\helpers\VarDumper::dumpAsString($e->getMessage(), 20),
                            'Airline:syncCabinClasses:noJsonDecode'
                        );
                    }
                }
            }
        }
    }

    /**
     * @param array $iata
     * @return array
     */
    public static function getAirlinesListByIata($iata = []): array
    {
        $data = [];
        $airlines = self::find()->where(['iata' => $iata])->asArray()->all();
        if($airlines) {
            foreach ($airlines as $airline) {
                $data[$airline['iata']] = $airline['name'];
            }
        }

        return $data;
    }
}
