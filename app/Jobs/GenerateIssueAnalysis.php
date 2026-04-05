<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Services\IssueAnalysisService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateIssueAnalysis implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Issue $issue,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(IssueAnalysisService $analysisService): void
    {
        try {
            $analysis = $analysisService->generateSummaryAndAction($this->issue);
            $this->issue->update($analysis);
        } catch (\Exception $e) {
            Log::error('[GenerateIssueAnalysis] Failed to analyze issue ' . $this->issue->id . ': ' . $e->getMessage());
        }
    }
}
