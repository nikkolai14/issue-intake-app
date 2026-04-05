<?php

namespace App\Services;

use Exception;
use App\Models\Issue;
use App\Enums\Priority;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Facades\Log;

class IssueAnalysisService
{
    public function generateSummaryAndAction(Issue $issue): array
    {
        try {
            $priorityLabel = $issue->priority?->label() ?? 'None';
            $statusLabel = $issue->status?->label() ?? 'Todo';
            
            $prompt = "Analyze this issue: Title: {$issue->title}, Description: {$issue->description}, Priority: {$priorityLabel}, Status: {$statusLabel}. Provide a JSON response with 'summary' (short summary under 100 words as a string) and 'next_action' (suggested next action as a numbered list string ready for display, e.g., '1. First step\\n2. Second step\\n3. Third step'). Return only simple strings, no arrays or complex structures.";
            
            $model = config('services.ollama.model', 'llama3.2');
            $response = Ollama::model($model)
                ->prompt($prompt)
                ->format('json')
                ->ask();
            
            $content = $response['response'] ?? '{}';
            $data = json_decode($content, true);

            Log::info('data: ' . print_r($data, true));
            if (json_last_error() === JSON_ERROR_NONE && isset($data['summary'], $data['next_action'])) {
                return [
                    'summary' => $data['summary'],
                    'next_action' => $data['next_action'],
                ];
            }
        } catch (Exception $e) {
            Log::error('[IssueAnalysisService] Error generating issue analysis: ' . $e->getMessage());
        }
        
        // Fallback if AI analysis fails
        return $this->generateFallbackSummaryAndAction($issue);
    }

    private function generateFallbackSummaryAndAction(Issue $issue): array
    {
        $summary = "Issue regarding: {$issue->title}";
        $nextAction = match ($issue->priority) {
            Priority::Urgent => '1. Handle immediately and notify stakeholders.',
            Priority::High => '1. Escalate immediately.',
            Priority::Medium => '1. Schedule for review in the next work cycle.',
            Priority::Low => '1. Monitor and address when resources are available.',
            Priority::None => '1. No immediate action required.',
            default => '1. Review and assign priority.',
        };

        return ['summary' => $summary, 'next_action' => $nextAction];
    }
}