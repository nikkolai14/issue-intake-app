<?php

namespace Tests\Feature;

use App\Enums\Priority;
use App\Enums\Status;
use App\Jobs\GenerateIssueAnalysis;
use App\Models\Issue;
use App\Models\User;
use App\Services\IssueAnalysisService;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class IssueAnalysisTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_ai_generates_summary_and_action_successfully(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for($user)->create([
            'title' => 'Login bug',
            'description' => 'Users cannot log in',
            'priority' => Priority::High,
            'status' => Status::Todo,
        ]);

        // Mock Ollama to return valid JSON
        Ollama::shouldReceive('model')
            ->with('llama3.2')
            ->andReturnSelf();
        Ollama::shouldReceive('prompt')
            ->andReturnSelf();
        Ollama::shouldReceive('format')
            ->with('json')
            ->andReturnSelf();
        Ollama::shouldReceive('ask')
            ->andReturn([
                'response' => '{"summary": "Users are unable to log in due to a bug.", "next_action": "Investigate the login endpoint immediately."}'
            ]);

        $service = app(IssueAnalysisService::class);
        $result = $service->generateSummaryAndAction($issue);

        $this->assertEquals('Users are unable to log in due to a bug.', $result['summary']);
        $this->assertEquals('Investigate the login endpoint immediately.', $result['next_action']);
    }

    public function test_fallback_works_when_ai_fails(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for($user)->create([
            'title' => 'Minor UI issue',
            'description' => 'Button color is wrong',
            'priority' => Priority::Low,
            'status' => Status::Todo,
        ]);

        // Mock Ollama to throw exception
        Ollama::shouldReceive('model')
            ->andThrow(new \Exception('AI service unavailable'));

        $service = app(IssueAnalysisService::class);
        $result = $service->generateSummaryAndAction($issue);

        $this->assertEquals('Issue regarding: Minor UI issue', $result['summary']);
        $this->assertEquals('Monitor and address when resources are available.', $result['next_action']);
    }

    public function test_fallback_for_high_priority(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for($user)->create([
            'title' => 'Critical error',
            'priority' => Priority::High,
        ]);

        Ollama::shouldReceive('model')
            ->andThrow(new \Exception('AI failed'));

        $service = app(IssueAnalysisService::class);
        $result = $service->generateSummaryAndAction($issue);

        $this->assertEquals('Escalate immediately.', $result['next_action']);
    }

    public function test_job_dispatches_on_issue_creation(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $this->actingAs($user)->post('/issues', [
            'title' => 'Test Issue',
            'description' => 'Description',
            'status' => Status::Todo->value,
            'priority' => Priority::Medium->value,
        ]);

        Queue::assertPushed(GenerateIssueAnalysis::class);
    }

    public function test_job_updates_issue_with_analysis(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $issue = Issue::factory()->for($user)->create();

        // Dispatch the job
        $job = new GenerateIssueAnalysis($issue);
        $service = Mockery::mock(IssueAnalysisService::class);
        $service->shouldReceive('generateSummaryAndAction')
            ->with($issue)
            ->andReturn([
                'summary' => 'AI generated summary',
                'next_action' => 'AI suggested action'
            ]);

        app()->instance(IssueAnalysisService::class, $service);

        $job->handle($service);

        $issue->refresh();
        $this->assertEquals('AI generated summary', $issue->summary);
        $this->assertEquals('AI suggested action', $issue->next_action);
    }
}
