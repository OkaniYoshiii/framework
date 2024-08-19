<?php

namespace Framework\Types;

use Framework\Contracts\Traits\SingletonTrait;

class Config
{
    use SingletonTrait;

    private array $data;

    private function __construct()
    {
        $this->data['local'] = json_decode(file_get_contents('../config/config.local.json'), true);
        $this->data['global'] = json_decode(file_get_contents('../config/config.global.json'), true);
    }

    public function get(string $environement, string $key) : ?array
    {
        return match($environement) {
            'local' => $this->data['local'][$key],
            'global' => $this->data['local'][$key],
            default => null,
        };
    }
}