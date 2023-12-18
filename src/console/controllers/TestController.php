<?php

namespace statikbe\carbontracker\console\controllers;

use craft\console\Controller;
use statikbe\carbontracker\CarbonTracker;

class TestController extends Controller
{
    public function actionIndex()
    {
        $data = CarbonTracker::getInstance()->api->getSite("https://www.statik.be");
        dd($data);
    }
}
