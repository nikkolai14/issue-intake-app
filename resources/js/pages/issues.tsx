import { Head, router, useHttp } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ColumnDef } from '@tanstack/react-table';
import { PlusIcon, EyeIcon, Loader2 } from 'lucide-react';
import { useState } from 'react';
import IssueForm from '@/pages/issues/issue-form';
import DeleteIssueDialog from '@/pages/issues/delete-issue-dialog';
import IssueDetailsDialog from '@/pages/issues/issue-details-dialog';
import PriorityFilterDropdown from '@/pages/issues/priority-filter-dropdown';
import CategoryFilterDropdown from '@/pages/issues/category-filter-dropdown';
import SettingsDropdown from '@/pages/issues/settings-dropdown';
import TruncatedTextTooltip from '@/pages/issues/truncated-text-tooltip';
import { Issue, IssueFormData, Enums } from '@/types/issues';
import { toast } from 'sonner';

type Props = Enums & {
    issues: Issue[];
};

type EditIssueData = Enums & {
    issue: IssueFormData;
};

export default function Issues({ issues, categories, priorities, statuses }: Props) {
    const [showCategoryModal, setShowCategoryModal] = useState(false);
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [deleteConfirmId, setDeleteConfirmId] = useState<number | null>(null);
    const [editData, setEditData] = useState<EditIssueData | null>(null);
    const [detailsIssue, setDetailsIssue] = useState<Issue | null>(null);
    const [statusFilter, setStatusFilter] = useState<string>('all');
    const [priorityFilter, setPriorityFilter] = useState<string>('all');
    const [categoryFilter, setCategoryFilter] = useState<string>('all');

    const http = useHttp();

    const handleEdit = (id: number) => {
        setDetailsIssue(null); // Close details dialog
        http.get(`/issues/${id}/edit`, {
            onSuccess: (data) => {
                setEditData(data as EditIssueData);
            },
        });
    };

    const handleDelete = (id: number) => {
        router.delete(`/issues/${id}`, {
            onSuccess: () => {
                setDeleteConfirmId(null);
                toast.success('Issue deleted successfully');
            },
        });
    };

    const filteredIssues = issues.filter(issue => {
        const matchesStatus = statusFilter === 'all' || issue.status.value === statusFilter;
        const matchesPriority = priorityFilter === 'all' || issue.priority.value === priorityFilter;
        const matchesCategory = categoryFilter === 'all' || 
            issue.categories.some(cat => cat.id === parseInt(categoryFilter));
        return matchesStatus && matchesPriority && matchesCategory;
    });

    const statusFilterOptions = [
        { value: 'all', label: 'All', variant: 'outline' as const },
        { value: 'todo', label: 'Todo', variant: 'default' as const },
        { value: 'in_progress', label: 'In Progress', variant: 'warning' as const },
        { value: 'in_review', label: 'In Review', variant: 'info' as const },
        { value: 'completed', label: 'Completed', variant: 'success' as const },
    ];

    const columns: ColumnDef<Issue>[] = [
        {
            accessorKey: 'title',
            header: 'Title',
            cell: ({ row }) => (
                <div className="font-medium">{row.original.title}</div>
            ),
        },
        {
            accessorKey: 'summary',
            header: 'Summary',
            cell: ({ row }) => {
                const content = row.original.summary;
                if (!content) {
                    return (
                        <div className="flex items-center gap-2 text-muted-foreground">
                            <Loader2 className="size-4 animate-spin" />
                            <span>Generating...</span>
                        </div>
                    );
                }
                
                return <TruncatedTextTooltip text={content} title="Summary" />;
            },
        },
        {
            accessorKey: 'next_action',
            header: 'Next Action',
            cell: ({ row }) => {
                const content = row.original.next_action;
                if (!content) {
                    return (
                        <div className="flex items-center gap-2 text-muted-foreground">
                            <Loader2 className="size-4 animate-spin" />
                            <span>Generating...</span>
                        </div>
                    );
                }
                
                return <TruncatedTextTooltip text={content} title="Next Action" />;
            },
        },
        {
            accessorKey: 'created_at',
            header: 'Date',
            cell: ({ row }) => (
                <div className="text-sm">{row.original.created_at}</div>
            ),
        },
        {
            id: 'actions',
            cell: ({ row }) => (
                <div className="flex justify-end">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setDetailsIssue(row.original)}
                    >
                        <EyeIcon className="size-4" />
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <>
            <Head title="Issues" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div className="flex gap-2">
                        <Button
                            onClick={() => setShowCreateModal(true)}
                        >
                            <PlusIcon className="size-4" />
                            Add Issue
                        </Button>
                        <div className="flex items-center gap-2">
                            {statusFilterOptions.map((option) => (
                                <Badge
                                    key={option.value}
                                    variant={statusFilter === option.value ? option.variant : 'outline'}
                                    className="cursor-pointer"
                                    onClick={() => setStatusFilter(option.value)}
                                >
                                    {option.label}
                                </Badge>
                            ))}
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <PriorityFilterDropdown
                            priorityFilter={priorityFilter}
                            priorities={priorities}
                            onFilterChange={setPriorityFilter}
                        />
                        <CategoryFilterDropdown
                            categoryFilter={categoryFilter}
                            categories={categories}
                            onFilterChange={setCategoryFilter}
                        />
                        <SettingsDropdown
                            onCategoriesClick={() => setShowCategoryModal(true)}
                        />
                    </div>
                </div>

                <DataTable columns={columns} data={filteredIssues} />
            </div>

            {showCreateModal && (
                <IssueForm
                    categories={categories}
                    priorities={priorities}
                    statuses={statuses}
                    open={showCreateModal}
                    onClose={() => setShowCreateModal(false)}
                />
            )}

            {editData && (
                <IssueForm
                    issue={editData.issue}
                    categories={editData.categories}
                    priorities={editData.priorities}
                    statuses={editData.statuses}
                    open={true}
                    onClose={() => setEditData(null)}
                />
            )}

            {deleteConfirmId && (
                <DeleteIssueDialog
                    deleteConfirmId={deleteConfirmId}
                    issueTitle={issues.find(issue => issue.id === deleteConfirmId)?.title}
                    onCancel={() => setDeleteConfirmId(null)}
                    onConfirm={handleDelete}
                />
            )}

            {detailsIssue && (
                <IssueDetailsDialog
                    issue={detailsIssue}
                    onClose={() => setDetailsIssue(null)}
                    onEdit={handleEdit}
                    onDelete={(id) => {
                        setDeleteConfirmId(id);
                        setDetailsIssue(null);
                    }}
                />
            )}
        </>
    );
}

Issues.layout = {
    breadcrumbs: [
        {
            title: 'Issues',
            href: '/issues',
        },
    ],
};
