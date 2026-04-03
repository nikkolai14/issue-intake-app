import { Head, router, useHttp } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ColumnDef } from '@tanstack/react-table';
import { PencilIcon, TrashIcon, PlusIcon, SettingsIcon } from 'lucide-react';
import { useState } from 'react';
import IssueForm from '@/pages/issues/issue-form';
import DeleteIssueDialog from '@/pages/issues/delete-issue-dialog';
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

    const http = useHttp();

    const handleEdit = (id: number) => {
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

    const columns: ColumnDef<Issue>[] = [
        {
            accessorKey: 'title',
            header: 'Title',
            cell: ({ row }) => (
                <div className="font-medium">{row.original.title}</div>
            ),
        },
        {
            accessorKey: 'description',
            header: 'Description',
            cell: ({ row }) => (
                <div className="max-w-md truncate">
                    {row.original.description}
                </div>
            ),
        },
        {
            accessorKey: 'status',
            header: 'Status',
            cell: ({ row }) => {
                const statusColors: Record<string, string> = {
                    todo: 'default',
                    in_progress: 'warning',
                    in_review: 'info',
                    completed: 'success',
                };
                return (
                    <Badge
                        variant={
                            statusColors[row.original.status?.value || 'todo']
                        }
                    >
                        {row.original.status.label || 'Todo'}
                    </Badge>
                );
            },
        },
        {
            accessorKey: 'priority',
            header: 'Priority',
            cell: ({ row }) => {
                if (!row.original.priority.label) {
                    return <span className="text-muted-foreground">-</span>;
                }
                const priorityColors: Record<string, string> = {
                    urgent: 'destructive',
                    high: 'default',
                    medium: 'secondary',
                    low: 'outline',
                    none: 'outline',
                };
                return (
                    <Badge
                        variant={
                            priorityColors[
                                row.original.priority.value || 'none'
                            ] as any
                        }
                    >
                        {row.original.priority.label}
                    </Badge>
                );
            },
        },
        {
            accessorKey: 'categories',
            header: 'Categories',
            cell: ({ row }) => {
                if (row.original.categories.length === 0) {
                    return <span className="text-muted-foreground">-</span>;
                }
                return (
                    <div className="flex flex-wrap gap-1">
                        {row.original.categories.map((category) => (
                            <Badge key={category.id} variant="outline">
                                {category.name}
                            </Badge>
                        ))}
                    </div>
                );
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
                <div className="flex justify-end gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => handleEdit(row.original.id)}
                    >
                        <PencilIcon className="size-4" />
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setDeleteConfirmId(row.original.id)}
                    >
                        <TrashIcon className="size-4" />
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
                    </div>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" size="sm">
                                <SettingsIcon className="mr-2 size-4" />
                                Settings
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem
                                onClick={() =>
                                    router.visit('/categories', {
                                        onSuccess: () =>
                                            setShowCategoryModal(true),
                                    })
                                }
                            >
                                Categories
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>

                <DataTable columns={columns} data={issues} />
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
