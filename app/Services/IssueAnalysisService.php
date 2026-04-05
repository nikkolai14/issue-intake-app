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
            
            $prompt = "Analyze this issue: Title: {$issue->title}, Description: {$issue->description}, Priority: {$priorityLabel}, Status: {$statusLabel}. Provide a JSON response with 'summary' (short summary under 100 words) and 'next_action' (suggested next action).";
            
            $model = config('services.ollama.model', 'llama3.2');
            $response = Ollama::model($model)
                ->prompt($prompt)
                ->format('json')
                ->ask();
            
            $content = $response['response'] ?? '{}';
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['summary'], $data['next_action'])) {
                // Handle case where next_action might be returned as an array
                $nextAction = $data['next_action'];
                if (is_array($nextAction)) {
                    $nextAction = is_string($nextAction[0] ?? null) ? $nextAction[0] : implode(', ', array_filter($nextAction, 'is_string'));
                }
                
                return [
                    'summary' => $data['summary'],
                    'next_action' => $nextAction,
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
            Priority::Urgent => 'Handle immediately and notify stakeholders.',
            Priority::High => 'Escalate immediately.',
            Priority::Medium => 'Schedule for review in the next work cycle.',
            Priority::Low => 'Monitor and address when resources are available.',
            Priority::None => 'No immediate action required.',
            default => 'Review and assign priority.',
        };

        return ['summary' => $summary, 'next_action' => $nextAction];
    }
}