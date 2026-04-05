<?php

namespace App\Controllers;

class SurfController
{
    public function handleRequest(string $method, string $uri): void
    {
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $this->getConditions();
    }

    private function getConditions(): void
    {
        $url = 'https://marine-api.open-meteo.com/v1/marine'
            . '?latitude=43.6&longitude=-1.5'
            . '&hourly=wave_height,wave_direction,wave_period'
            . '&timezone=Europe/Paris'
            . '&forecast_days=1';

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 10,
                'header'  => "Accept: application/json\r\n",
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            http_response_code(502);
            echo json_encode(["error" => "Failed to fetch surf conditions from external API"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $data = json_decode($response, true);

        if (!$data || !isset($data['hourly'])) {
            http_response_code(502);
            echo json_encode(["error" => "Invalid response from marine API"], JSON_UNESCAPED_SLASHES);
            return;
        }

        $hourly = $data['hourly'];
        $conditions = [];

        for ($i = 0; $i < count($hourly['time']); $i++) {
            $time = date('H:i', strtotime($hourly['time'][$i]));
            $conditions[] = [
                'time'               => $time,
                'wave_height_m'      => $hourly['wave_height'][$i] ?? null,
                'wave_direction_deg' => $hourly['wave_direction'][$i] ?? null,
                'wave_period_s'      => $hourly['wave_period'][$i] ?? null,
            ];
        }

        echo json_encode([
            'location'   => 'Hossegor, France',
            'updated_at' => date('c'),
            'conditions'  => $conditions,
        ], JSON_UNESCAPED_SLASHES);
    }
}
