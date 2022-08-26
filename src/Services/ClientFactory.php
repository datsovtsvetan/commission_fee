<?php

namespace App\Services;

use App\Model\BaseClient;
use App\Model\BusinessClient;
use App\Model\PrivateClient;

class ClientFactory
{
    private array $clients;
    private array $withdrows;

    public function __construct()
    {
        $this->clients = [];
        $this->withdrows = [];

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

    /**
     * @return array
     */
    public function getClients(): array
    {
        return $this->clients;
    }

    public function findById(int $id):?BaseClient
    {
        foreach ($this->clients as $client){
            if($client->getId() == $id){
                return $client;
            }
        }
        return null;
    }

//    public function addWithdraw(int $clientId, float $amount, string $currency):void
//    {
//        $client = $this->findById($clientId);
//    }


}