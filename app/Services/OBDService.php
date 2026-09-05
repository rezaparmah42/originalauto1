<?php

namespace App\Services;

class OBDService
{
    public function connectELM327($device = 'ELM327')
    {
        return [
            'connected' => true,
            'device' => $device,
            'status' => 'connected',
        ];
    }

    public function sendCommand($command)
    {
        return [
            'command' => $command,
            'response' => '41 0C 1A',
        ];
    }

    public function readResponse($rawResponse)
    {
        return trim((string) $rawResponse);
    }

    public function parseDTC($response)
    {
        preg_match_all('/P\d{4}/', (string) $response, $matches);
        return array_values(array_unique($matches[0] ?? []));
    }

    public function clearCodes()
    {
        return [
            'success' => true,
            'message' => 'کدها پاک شد.',
        ];
    }
}
