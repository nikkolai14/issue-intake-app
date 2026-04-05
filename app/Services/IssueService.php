<?php

namespace App\Services;

use App\Jobs\GenerateIssueAnalysis;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class IssueService
{
    /**
     * Get issues for a user with optional filters.
     */
    public function getIssuesForUser(User $user, array $filters = []): Collection
    {
        $query = $user->issues()->with('categories');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        return $query->latest()->get();
    }

    /**
     * Create a new issue for a user.
     */
    public function createIssueForUser(User $user, array $data): Issue
    {
        $issue = $user->issues()->create($data);

        if (isset($data['category_ids'])) {
            $issue->categories()->sync($data['category_ids']);
        }

        // Dispatch job to generate summary and action in background
        GenerateIssueAnalysis::dispatch($issue);

        return $issue->load('categories');
    }

    /**
     * Update an existing issue.
     */
    public function updateIssue(Issue $issue, array $data): Issue
    {
        $issue->update($data);

        if (isset($data['category_ids'])) {
            $issue->categories()->sync($data['category_ids']);
        }

        // Dispatch job to regenerate summary and action in background
        GenerateIssueAnalysis::dispatch($issue);

        return $issue->load('categories');
    }

    /**
     * Delete an issue.
     */
    public function deleteIssue(Issue $issue): void
    {
        $issue->delete();
    }
}
