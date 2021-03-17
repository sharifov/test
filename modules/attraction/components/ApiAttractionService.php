<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\attraction\components;

use modules\attraction\models\Attraction;
use modules\attraction\models\forms\AttractionOptionsFrom;
use modules\attraction\models\forms\AvailabilityPaxFrom;
use yii\base\Component;
use yii\httpclient\Request;
use Datetime;
use yii\helpers\VarDumper;

/**
 * Class ApiAttractionService
 * @package modules\attraction\components
 *
 * @property string $url
 * @property string $apiKey
 * @property string $secret
 * @property Request $request
 */

class ApiAttractionService extends Component
{
    public string $url;
    public string $apiKey;
    public string $secret;
    //public $options = [CURLOPT_ENCODING => 'gzip'];

    public function init(): void
    {
        parent::init();
        //$this->initRequest();
    }

    /**
     * @return bool
     */
    /*private function initRequest(): bool
    {
        $authStr = base64_encode($this->username . ':' . $this->password);

        try {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $this->request = $client->createRequest();
            $this->request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
            return true;
        } catch (\Throwable $throwable) {
            \Yii::error(VarDumper::dumpAsString($throwable, 10), 'ApiAttractionService::initRequest:Throwable');
        }

        return false;
    }*/

    private function execRequest($query)
    {
        $graphqlEndpoint = $this->url;
        $apiKey = $this->apiKey;
        $secret = $this->secret;

        $dt = new DateTime();
        $date = $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z';

        $string = $date . $apiKey . 'POST/graphql' . $query;
        $base64HashSignature = base64_encode(hash_hmac('sha1', $string, $secret, true));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $graphqlEndpoint);

        $headers = [];
        $headers[] = "x-api-key: $apiKey";
        $headers[] = "x-holibob-date: $date";
        $headers[] = "x-holibob-signature: $base64HashSignature";
        $headers[] = "x-holibob-currency: USD";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    public function inputPriceCategoryToAvailability(AvailabilityPaxFrom $priceCategoryModel): array
    {
        $queryParamsDefinition = '$availabilityId: String!';
        $queryInputParamsDefinition = '';
        $queryVariables = '{"availabilityId":"' . $priceCategoryModel->availability_id . '"';

        $pax = $priceCategoryModel->pax_quantity;

        foreach ($pax as $key => $paxCategory) {
            $queryParamsDefinition .= ' $pricingCategoryId' . $key . ': String! $paxQuantity' . $key . ': Int!';
            $queryInputParamsDefinition .= '{id: $pricingCategoryId' . $key . ' value: $paxQuantity' . $key . '} ';
            $queryVariables .= ', "pricingCategoryId' . $key . '":"' . key($paxCategory) . '", "paxQuantity' . $key . '":' . $paxCategory[key($paxCategory)] . '';
        }
        $queryVariables .= '}';

        $query = [
            'query' => 'query holibob(
                    ' . $queryParamsDefinition . '
                ){
                    availability(
                        id: $availabilityId
                        input: {
                            pricingCategoryList: [
                                ' . $queryInputParamsDefinition . '
                            ]
                        } 
                    ){
                        id
                        productId
                        date
                        isValid
                        durationMinutes
                        optionList {
                            nodes {
                                id
                                label
                                dataType
                                isAnswered
                                dataFormat
                                availableOptions {
                                    label
                                    value
                                }
                                answerValue
                                answerFormattedText
                            }
                        }
                        pricingCategoryList {
                            priceTotalFormattedText
                            priceTotal
                            currency
                            nodes {
                                id
                                label
                                value
                                isValid
                                minParticipants
                                maxParticipants
                                maxParticipantsDepends {
                                    pricingCategoryId
                                    multiplier
                                    explanation
                                }
                                minAge
                                maxAge
                                price
                                currency                                
                                priceFormattedText
                                priceTotal
                                priceTotalFormattedText
                            }
                        }
                    }
                }',
            'variables' => $queryVariables,
            'operationName' => 'holibob'
        ];

        $result = self::execRequest(@json_encode($query));
        $data = json_decode($result, true);
        return $data['data'] ?? [];
    }

    public function inputOptionsToAvailability(AttractionOptionsFrom $optionsModel): array
    {
        $queryParamsDefinition = '$availabilityId: String!';
        $queryInputParamsDefinition = '';
        $queryVariables = '{"availabilityId":"' . $optionsModel->availability_id . '"';

        $options = $optionsModel->selected_options;

        foreach ($options as $key => $option) {
            $queryParamsDefinition .= ' $optionId' . $key . ': String! $optionValue' . $key . ': String!';
            $queryInputParamsDefinition .=  '{id: $optionId' . $key . ' value: $optionValue' . $key . '} ';
            $queryVariables .= ', "optionId' . $key . '":"' . key($option) . '", "optionValue' . $key . '":"' . $option[key($option)] . '"';
        }
        $queryVariables .= '}';

        $query = [
            'query' => 'query holibob(
                    ' . $queryParamsDefinition . '
                ){
                    availability(
                        id: $availabilityId
                        input: {
                            optionList: [
                                ' . $queryInputParamsDefinition . '
                            ]
                        }
                    ){
                        id                        
                        isValid
                        optionList {
                            nodes {
                                id
                                label
                                dataType
                                dataFormat
                                availableOptions {
                                    label
                                    value
                                }
                                answerValue
                                answerFormattedText
                            }
                        }
                        pricingCategoryList {
                            priceTotalFormattedText
                            nodes {
                                id
                                label
                                value
                                isValid
                                minParticipants
                                maxParticipants
                                maxParticipantsDepends {
                                    pricingCategoryId
                                    multiplier
                                    explanation
                                }
                                minAge
                                maxAge
                                price
                                currency
                                priceFormattedText
                                priceTotal
                                priceTotalFormattedText
                            }
                        }
                    }
                }',
            'variables' => $queryVariables,
            'operationName' => 'holibob'
        ];

        $result = self::execRequest(@json_encode($query));
        $data = json_decode($result, true);
        //VarDumper::dump($result, 10, true); exit();
        return $data['data'] ?? [];
    }

    public function getAvailability(string $availabilityId): array
    {
        $query = [
            'query' => 'query holibob($availabilityId: String!) {
                availability(id: $availabilityId) {
                    id
                    isValid
                    durationFormattedText
                    durationMinutes
                    optionList {
                        nodes {
                            id
                            label
                            dataType
                            dataFormat
                            availableOptions {
                                label
                                value
                            }
                            answerValue
                            answerFormattedText
                        }
                    }
                    pricingCategoryList {
                        nodes{
                            id
                            label
                            value
                            isValid
                            minParticipants
                            maxParticipants
                            maxParticipantsDepends {
                              pricingCategoryId
                              multiplier
                              explanation
                            }
                            minAge
                            maxAge
                            price
                            currency
                            priceFormattedText
                            priceTotal
                            priceTotalFormattedText
                        }
                    }
                }
            }',
            'variables' => '{"availabilityId":"' . $availabilityId . '"}',
            'operationName' => 'holibob',
        ];

        $result = self::execRequest(@json_encode($query));
        //VarDumper::dump($result, 10, true); die();
        $data = json_decode($result, true);
        return $data['data'] ?? [];
    }

    public function getAvailabilityList(string $productId, Attraction $attraction): array
    {
        $query = [
            'query' => 'query holibob ($productId: String!, $startDate: Date, $endDate: Date) {
              availabilityList(
                productId: $productId, 
                filter: {
                  startDate: $startDate, 
                  endDate:  $endDate
                }
              ) {
                recordCount
                pageCount
                nodes {
                  id      
                  date
                  soldOut
                  guidePriceFormattedText
                }
              }
            }',
            'variables' => '{"productId":"' . $productId . '", "startDate":"' . $attraction->atn_date_from . '", "endDate":"' . $attraction->atn_date_to . '"}',
            'operationName' => 'holibob'
        ];

        $result = self::execRequest(@json_encode($query));
        $data = json_decode($result, true);
        return $data['data'] ?? [];
    }

    public function getProductById(string $productId): array
    {
        $query = [
            'query' => 'query holibob ($productId: String!){
              product(id: $productId) {
                id
                code
                name                
                guidePriceCurrency
                guidePrice
                difficultyLevel
                supplierName
                __typename
                previewImage {
                  url
                }
                contentList {nodes{
                  name
                  type
                  description
                }}
              }
            }',
            'variables' => '{"productId":"' . $productId . '"}',
            'operationName' => 'holibob',
        ];

        $result = self::execRequest(@json_encode($query));
        $data = json_decode($result, true);
        return $data['data'] ?? [];
    }

    public function getProductList(Attraction $attraction): array
    {
        $query = [
            'query' => 'query holibob ($term: String!){
              productList(filter: {search: $term}) {
                recordCount
                pageCount 
                nodes {
                  id
                  name  
                  availabilityType
                  guidePriceFormattedText
                  guidePrice
                  supplierName
                  abstract
                  previewImage {
                     url
                  }
                   place {
                     cityId
                     cityName                    
                     countryId
                     countryName
                   }
                }
              }
            }',
            'variables' => '{"term":"' . $attraction->atn_destination . '"}',
            'operationName' => 'holibob',
        ];

        $result = self::execRequest(@json_encode($query));
        $data = json_decode($result, true);
        return $data['data'] ?? [];
    }

    public function checkApi()
    {
        //$query = '{"query":"query {welcome}"}';
        //$query = ['query' => 'query {welcome}'];
        //$result = self::execRequest(@json_encode($query));
        //var_dump($result); die();
    }
}
