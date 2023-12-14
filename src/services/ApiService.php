<?php

namespace statikbe\carbontracker\services;


use craft\base\Component;
use GuzzleHttp\Client;
use statikbe\carbontracker\models\SiteStatisticsModel;

class ApiService extends Component
{

    private const API_URL = "https://api.websitecarbon.com";

    private Client $client;


    public function init(): void
    {
        $this->client = new Client([
            'base_uri' => self::API_URL
        ]);
        parent::init();
    }

    public function getSite(string $url): SiteStatisticsModel
    {
        $data = $this->makeRequest('/site', [
            'query' => [
                'url' => $url
            ]
        ]);

        $model = new SiteStatisticsModel();
        $model->setAttributes([
            'url' => $data['url'],
            'green' => $data['green'],
            'bytes' => $data['bytes'],
            'cleanerThan' => $data['cleanerThan'],
            'rating' => $data['rating'],
        ]);
        return $model;
    }

    private function makeRequest($endpoint, $data)
    {
        $request = $this->client->get($endpoint, $data);
        return json_decode($request->getBody()->getContents(), true);
    }
}