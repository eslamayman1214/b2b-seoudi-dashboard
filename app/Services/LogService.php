<?php

namespace App\Services;

use App\Helpers\LogHelper;

class LogService
{
    public function logAction($action, $message)
    {
        LogHelper::logAction($action, $message);
    }
}