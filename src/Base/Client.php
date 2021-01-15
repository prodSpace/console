<?php

declare(strict_types=1);

namespace Console\Base;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class Client
 * @package Console\Base
 */
class Client
{
    /**
     * @return HttpClientInterface
     */
    public static function getInstance(): HttpClientInterface
    {
        return HttpClient::create();
    }
}