<?php

namespace statikbe\carbontracker\services;

use craft\base\Component;
use craft\elements\Entry;
use GuzzleHttp\Client;
use statikbe\carbontracker\models\SiteStatisticsModel;

class ApiService extends Component
{

    private const API_URL = "https://api.websitecarbon.com";
    public const READ_MORE_LINK = 'https://www.websitecarbon.com/introducing-the-website-carbon-rating-system/';

    /**
     * @var Client
     */
    private Client $client;

    public function init(): void
    {
        $this->client = new Client([
            'base_uri' => self::API_URL,
        ]);
        parent::init();
    }

    public function getSite(Entry $entry): SiteStatisticsModel
    {
        $model = new SiteStatisticsModel();

        if (getenv('CRAFT_ENVIRONMENT') === 'dev') {
            $url = self::READ_MORE_LINK;
        } else {
            $url = $entry->getUrl();
        }

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
