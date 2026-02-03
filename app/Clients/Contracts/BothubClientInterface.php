<?php

namespace App\Clients\Contracts;

interface BothubClientInterface
{
    public function generateImage(string $prompt): array;
}
