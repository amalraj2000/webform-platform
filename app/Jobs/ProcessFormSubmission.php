<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Submission;

class ProcessFormSubmission implements ShouldQueue
{
    use Queueable;

    public $submissionId;
    public $formVersionId;
    public $data;
    public $ipAddress;

    public function __construct(string $submissionId, int $formVersionId, array $data, ?string $ipAddress = null)
    {
        $this->submissionId = $submissionId;
        $this->formVersionId = $formVersionId;
        $this->data = $data;
        $this->ipAddress = $ipAddress;
    }

    public function handle(): void
    {
        Submission::create([
            'id' => $this->submissionId,
            'form_version_id' => $this->formVersionId,
            'data' => $this->data,
            'ip_address' => $this->ipAddress,
        ]);
    }
}
