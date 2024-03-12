<?php

namespace statikbe\carbontracker\services;

use craft\base\Component;
use craft\elements\Entry;
use GuzzleHttp\Client;
use statikbe\carbontracker\CarbonTracker;
use statikbe\carbontracker\models\SiteStatisticsModel;

class ApiService extends Component
{
    private const API_URL = "https://api.websitecarbon.com";
    public const READ_MORE_LINK = 'https://www.websitecarbon.com/introducing-the-website-carbon-rating-system/';

    private Client $client;

    public function init(): void
    {
        $this->client = new Client([
            'base_uri' => self::API_URL,
        ]);
        parent::init();
    }

    public function getSite(Entry $entry): SiteStatisticsModel|bool
    {
        $model = new SiteStatisticsModel();

        if (getenv('CRAFT_ENVIRONMENT') === 'dev') {
            $json_data = '{
                "url": "https://www.wholegraindigital.com/",
                "green": true,
                "rating": "A+",
                "bytes": 443854,
                "cleanerThan": 0.83,
                "statistics": {
                    "adjustedBytes": 335109.77,
                    "energy": 0.0005633320052642376,
                    "co2": {
                        "grid": {
                            "grams": 0.26758270250051286,
                            "litres": 0.14882949913078525
                        },
                        "renewable": {
                            "grams": 0.24250694721722435,
                            "litres": 0.13488236404222018
                        }
                    }
                }
            }';

            $data = json_decode($json_data, true);
        } else {
            try {
                $url = $entry->getUrl();
                $data = $this->makeRequest('/site', [
                    'query' => [
                        'url' => $url,
                    ],
                ]);
            } catch (\Exception $e) {
                \Craft::error($e->getMessage(), CarbonTracker::class);
                return false;
            }
        }


        if (empty($data)) {
            return $model;
        }

        $model->setAttributes([
            'entryId' => $entry->id,
            'siteId' => $entry->siteId,
            'url' => $data['url'],
            'green' => $data['green'],
            'bytes' => $data['bytes'],
            'cleanerThan' => $data['cleanerThan'],
            'rating' => $data['rating'],
        ]);

        return $model;
    }

    /**
     * @param string $endpoint
     * @param non-empty-array<string, array> $data
     * @return non-empty-array<array>|null
     */
    private function makeRequest(string $endpoint, array $data): array|null
    {
        $request = $this->client->get($endpoint, $data);
        return json_decode($request->getBody()->getContents(), true);
    }
}
