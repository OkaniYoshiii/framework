<?php

namespace App;

use App\Traits\SingletonTrait;

class Config
{
    use SingletonTrait;

    private array $data;
    private array $databaseConfig;
    private string $secret;

    private function __construct()
    {
        $this->data = json_decode(file_get_contents('../config/config.local.json'), true);
        $this->databaseConfig = $this->data['database'];
        $this->secret = $this->data['secret'];
    }

    /**
     * Get the value of databaseConfig
     */ 
    public function getDatabaseConfig()
    {
        return $this->databaseConfig;
    }

    /**
     * Get the value of secret
     */ 
    public function getSecret()
    {
        return $this->secret;
    }
}