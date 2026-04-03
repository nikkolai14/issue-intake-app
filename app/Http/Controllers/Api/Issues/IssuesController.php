<?php

namespace App\Http\Controllers\Api\Issues;

use App\Enums\Priority;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Issues\IssueStoreRequest;
use App\Http\Requests\Issues\IssueUpdateRequest;
use App\Http\Resources\Issues\IssueResource;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssuesController extends Controller
{
    /**
     * Get all issues with optional filters for status, priority, and category.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()
            ->issues()
            ->with('categories');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->input('category_id'));
            });
        }

        $issues = $query->latest()->get();

        return response()->json([
            'data' => IssueResource::collection($issues),
        ]);
    }

    /**
     * Create a new issue.
     */
    public function store(IssueStoreRequest $request): JsonResponse
    {
        $issue = $request->user()->issues()->create($request->validated());

        if ($request->has('category_ids')) {
            $issue->categories()->sync($request->input('category_ids'));
        }

        $issue->load('categories');

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

        $issue->update($request->validated());

        if ($request->has('category_ids')) {
            $issue->categories()->sync($request->input('category_ids'));
        }

        $issue->load('categories');

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

        $issue->delete();

        return response()->json([
            'message' => 'Issue deleted successfully',
        ]);
    }
}
