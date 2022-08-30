<?php

namespace App\Services;

use App\Model\BusinessClient;
use App\Model\PrivateClient;

class ClientFactory
{
    private array $clients;

    public function __construct()
    {
        $this->clients = [];
    }

    public function createClientIfNotExist(int $id, string $type):void
    {
        foreach ($this->clients as $client){
            if($client->getId() == $id){
                return;
            }
        }

        switch ($type) {
            case 'private':
                $newClient = new PrivateClient($id);
                $this->clients[] = $newClient;
                break;
            case 'business':
                $newClient = new BusinessClient($id);
                $this->clients[] = $newClient;
                break;
        }

    }

    public function findById(int $id): PrivateClient|BusinessClient|null
    {
        foreach ($this->clients as $client){
            if($client->getId() == $id){
                return $client;
            }
        }

        return null;
    }
}