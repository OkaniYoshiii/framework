<?php

namespace OkaniYoshiii\Framework;

use Dotenv\Dotenv;

class App
{
    public const FRAMEWORK_DIR = __DIR__ . '/../';
    public const CACHE_DIR = __DIR__ . '/../cache/';

    public static function loadEnvVariables() : void
    {
        $dotenv = Dotenv::createImmutable('./', '.env.local');
        $dotenv->load();
    }
}