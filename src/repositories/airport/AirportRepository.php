<?php

namespace src\repositories\airport;

use common\models\Airports;
use src\repositories\NotFoundException;
use yii\db\ActiveRecord;

/**
 * Class AirportRepository
 */
class AirportRepository
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

    public function findByIata($iata): Airports
    {
        if ($airport = Airports::findOne(['iata' => $iata])) {
            return $airport;
        }
        throw new NotFoundException('Airport by IATA not found: ' . $iata);
    }

    public function getByIata($iata): ?Airports
    {
        if ($airport = Airports::findOne(['iata' => $iata])) {
            return $airport;
        }
        return null;
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
    public function getListForSearch($term, ?bool $disabled = false): array
    {
        $countTerm = mb_strlen($term);

        $query = Airports::find();

        if ($countTerm < 4) {
            $query->orFilterWhere(['like', 'LOWER(iata)', $term]);
        }

        if ($countTerm > 3) {
            $query->orFilterWhere(['like', 'LOWER(name)', $term]);
            $query->orFilterWhere(['like', 'LOWER(city)', $term]);
            $query->orFilterWhere(['like', 'LOWER(country)', $term]);
        }

        if ($disabled !== null) {
            $query->andWhere(['a_disabled' => $disabled]);
        }

        $query->orderBy(['iata' => SORT_ASC]);
        $query->limit(30);
        return $query->all();
    }
}
