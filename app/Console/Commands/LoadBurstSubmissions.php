<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Form;
use Illuminate\Support\Facades\Http;

class LoadBurstSubmissions extends Command
{
    protected $signature = 'test:burst-submissions {uuid} {count=100}';
    protected $description = 'Simulate a massive burst of submissions to test queue saturation and 202 Accepted ingestion.';

    public function handle()
    {
        $uuid = $this->argument('uuid');
        $count = (int) $this->argument('count');

        $form = Form::where('uuid', $uuid)->first();
        if (!$form || !$form->publishedVersion) {
            $this->error("Form not found or not published.");
            return;
        }

        $schema = $form->publishedVersion->schema;
        $url = url("/api/v1/forms/{$uuid}/submissions");

        $this->info("Starting burst of {$count} submissions to {$url}...");

        $success = 0;
        $failed = 0;

        for ($i = 0; $i < $count; $i++) {
            $payload = [];
            foreach ($schema as $field) {
                if ($field['type'] === 'email') {
                    $payload[$field['name']] = "test{$i}@example.com";
                } elseif ($field['type'] === 'number') {
                    $payload[$field['name']] = $i;
                } else {
                    $payload[$field['name']] = "Test Value {$i}";
                }
            }

            // Randomize IP to bypass 60req/min rate limiter for load testing
            $randomIp = rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);

            $response = Http::withHeaders(['X-Forwarded-For' => $randomIp])
                            ->post($url, $payload);

            if ($response->status() === 202) {
                $success++;
            } else {
                $failed++;
                if ($failed === 1) {
                    $this->error("First failure: " . $response->body());
                }
            }
        }

        $this->info("Burst complete. Success (HTTP 202): {$success}, Failed: {$failed}");
        $this->info("Check your queue worker to monitor database persistence rate.");
    }
}
