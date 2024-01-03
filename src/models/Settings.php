<?php

namespace statikbe\carbontracker\models;

use craft\base\Model;
use statikbe\carbontracker\services\ApiService;

/**
 * carbon-tracker settings
 */
class Settings extends Model
{
    public string $readmoreLink = ApiService::READ_MORE_LINK;
}
