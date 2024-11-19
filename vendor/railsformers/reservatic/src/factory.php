<?php

declare(strict_types=1);

namespace Reservatic;
class Api
{
    public static function reservatic(string $resUrl, string $apiToken, string $certificate, string $certificatePassword)
    {
        return new Reservatic($resUrl,$apiToken,$certificate,$certificatePassword);
    }
}