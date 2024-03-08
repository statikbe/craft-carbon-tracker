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
            $url = self::READ_MORE_LINK;
        } else {
            $url = $entry->getUrl();
        }

        try {
            $data = $this->makeRequest('/site', [
                'query' => [
                    'url' => $url,
                ],
            ]);

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
        } catch (\Exception $e) {
            \Craft::error($e->getMessage(), CarbonTracker::class);
            return false;
        }
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
