<?php

namespace sales\repositories\airport;

use common\models\Airports;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;
use yii\db\ActiveRecord;

/**
 * Class AirportRepository
 * @method null|Airports get(int $id)
 * @method null|Airports getByIata($iata)
 */
class AirportRepository extends Repository
{

    /**
     * @param int $id
     * @return Airports
     */
    public function find(int $id): Airports
    {
        if ($airport = Airports::findOne($id)) {
            return $airport;
        }
        throw new NotFoundException('Airport is not found.');
    }

    /**
     * @param $iata
     * @return Airports
     */
    public function findByIata($iata): Airports
    {
        if ($airport = Airports::findOne(['iata' => $iata])) {
            return $airport;
        }
        throw new NotFoundException('Airport (' . $iata . ') is not found.');
    }

    /**
     * @param $iata
     * @return bool
     */
    public function iataExists($iata): bool
    {
        return Airports::find()->where(['iata' => $iata])->exists();
    }

    /**
     * @param array $iata
     * @return array
     */
    public function getListByIata($iata = []): array
    {
        $data = [];
        foreach (Airports::find()->where(['iata' => $iata])->all() as $airport) {
            $data[$airport['iata']] = ['name' => $airport['name'], 'city' => $airport['city'], 'country' => $airport['country']];
        }
        return $data;
    }

    /**
     * @param $term
     * @return array|ActiveRecord[]
     */
    public function getListForSearch($term): array
    {
        $countTerm = mb_strlen($term);

        $query = Airports::find();

        if ($countTerm < 4) {
            $query->orfilterWhere(['like', 'LOWER(iata)', $term]);
        }

        if ($countTerm > 3) {
            $query->orFilterWhere(['like', 'LOWER(name)', $term]);
            $query->orFilterWhere(['like', 'LOWER(city)', $term]);
            $query->orFilterWhere(['like', 'LOWER(country)', $term]);
        }

        $query->orderBy(['iata' => SORT_ASC]);
        $query->limit(30);
        return $query->all();
    }
}
