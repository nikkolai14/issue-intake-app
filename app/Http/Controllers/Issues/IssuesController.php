<?php

namespace App\Http\Controllers\Issues;

use App\Enums\Priority;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Issues\IssueStoreRequest;
use App\Http\Requests\Issues\IssueUpdateRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IssuesController extends Controller
{
    /**
     * Display a listing of issues.
     */
    public function index(Request $request): Response
    {
        $issues = $request->user()
            ->issues()
            ->with('categories')
            ->latest()
            ->get()
            ->map(fn (Issue $issue) => [
                'id' => $issue->id,
                'title' => $issue->title,
                'description' => $issue->description,
                'priority' => [
                    'value' => $issue->priority?->value,
                    'label' => $issue->priority?->label(),
                ],
                'status' => [
                    'value' => $issue->status->value,
                    'label' => $issue->status->label(),
                ],
                'categories' => $issue->categories->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                ]),
                'created_at' => $issue->created_at->diffForHumans(),
            ]);

        $categories = $request->user()
            ->categories()
            ->latest()
            ->get()
            ->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
            ]);

        return Inertia::render('issues', [
            'issues' => $issues,
            'categories' => $categories,
            'priorities' => Priority::options(),
            'statuses' => Status::options(),
        ]);
    }

    /**
     * Store a newly created issue.
     */
    public function store(IssueStoreRequest $request): RedirectResponse
    {
        $issue = $request->user()->issues()->create($request->validated());

        if ($request->has('category_ids')) {
            $issue->categories()->sync($request->input('category_ids'));
        }

        return to_route('issues.index');
    }

    /**
     * Show the form for editing the specified issue.
     */
    public function edit(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);

        $categories = $request->user()
            ->categories()
            ->latest()
            ->get()
            ->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
            ]);

        $issueData = [
            'id' => $issue->id,
            'title' => $issue->title,
            'description' => $issue->description,
            'priority' => $issue->priority?->value,
            'status' => $issue->status->value,
            'category_ids' => $issue->categories->pluck('id')->toArray(),
        ];

        return response()->json([
            'issue' => $issueData,
            'categories' => $categories,
            'priorities' => Priority::options(),
            'statuses' => Status::options(),
        ]);
    }

    /**
     * Update the specified issue.
     */
    public function update(IssueUpdateRequest $request, Issue $issue): RedirectResponse
    {
        $this->authorize('update', $issue);

        $issue->update($request->validated());

        if ($request->has('category_ids')) {
            $issue->categories()->sync($request->input('category_ids'));
        }

        return to_route('issues.index');
    }

    /**
     * Remove the specified issue.
     */
    public function destroy(Issue $issue): RedirectResponse
    {
        $this->authorize('delete', $issue);

        $issue->delete();

        return to_route('issues.index');
    }
}
