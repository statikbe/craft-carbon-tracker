<?php

namespace statikbe\carbontracker\console\controllers;

use craft\console\Controller;
use craft\elements\Entry;
use statikbe\carbontracker\CarbonTracker;

class TestController extends Controller
{
    public function actionIndex(int $id): void
    {
        $entry = Entry::findOne(['id' => $id]);
        $data = CarbonTracker::getInstance()->api->getSite($entry);
        dd($data);
    }


}
