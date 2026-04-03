<?php

namespace App\Http\Controllers\Api\Issues;

use App\Enums\Priority;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Issues\IssueStoreRequest;
use App\Http\Requests\Issues\IssueUpdateRequest;
use App\Http\Resources\Issues\IssueResource;
use App\Models\Issue;
use App\Services\IssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssuesController extends Controller
{
    public function __construct(private IssueService $issueService) {}

    /**
     * Get all issues with optional filters for status, priority, and category.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'priority', 'category_id']);
        $issues = $this->issueService->getIssuesForUser($request->user(), $filters);

        return response()->json([
            'data' => IssueResource::collection($issues),
        ]);
    }

    /**
     * Create a new issue.
     */
    public function store(IssueStoreRequest $request): JsonResponse
    {
        $issue = $this->issueService->createIssueForUser($request->user(), $request->validated());

        return response()->json([
            'message' => 'Issue created successfully',
            'data' => new IssueResource($issue),
        ], 201);
    }

    /**
     * Get a specific issue.
     */
    public function show(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('view', $issue);

        $issue->load('categories');

        return response()->json([
            'data' => new IssueResource($issue),
        ]);
    }

    /**
     * Update an issue.
     */
    public function update(IssueUpdateRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);

        $issue = $this->issueService->updateIssue($issue, $request->validated());

        return response()->json([
            'message' => 'Issue updated successfully',
            'data' => new IssueResource($issue),
        ]);
    }

    /**
     * Delete an issue.
     */
    public function destroy(Issue $issue): JsonResponse
    {
        $this->authorize('delete', $issue);

        $this->issueService->deleteIssue($issue);

        return response()->json([
            'message' => 'Issue deleted successfully',
        ]);
    }
}
