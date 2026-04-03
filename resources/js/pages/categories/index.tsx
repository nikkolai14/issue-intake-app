import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/data-table';
import { ColumnDef } from '@tanstack/react-table';
import { PencilIcon, TrashIcon, PlusIcon } from 'lucide-react';
import { useState } from 'react';
import EditCategoryDialog from './edit-category-dialog';
import DeleteCategoryDialog from './delete-category-dialog';
import AddCategoryForm from './add-category-form';
import { Category } from '@/types/issues';

type Props = {
    categories: Category[];
};

export default function CategoryManagement({ categories }: Props) {
    const [editingCategory, setEditingCategory] = useState<Category | null>(
        null,
    );
    const [deletingCategory, setDeletingCategory] = useState<Category | null>(
        null,
    );

    const columns: ColumnDef<Category>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) => (
                <div className="font-medium">{row.original.name}</div>
            ),
        },
        {
            accessorKey: 'created_at',
            header: 'Created',
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
                        onClick={() => setEditingCategory(row.original)}
                    >
                        <PencilIcon className="size-4" />
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setDeletingCategory(row.original)}
                    >
                        <TrashIcon className="size-4" />
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <>
            <Head title="Manage Categories" />
            <div className="flex h-full flex-1 flex-col overflow-x-auto rounded-xl p-4">
                <Heading
                    title="Manage Categories"
                    description="Add or edit categories for organizing your issues."
                />

                <div className="space-y-4">
                    <AddCategoryForm />

                    <DataTable columns={columns} data={categories} />
                </div>

                <EditCategoryDialog
                    category={editingCategory}
                    onClose={() => setEditingCategory(null)}
                />

                <DeleteCategoryDialog
                    category={deletingCategory}
                    onClose={() => setDeletingCategory(null)}
                />
            </div>
        </>
    );
}

CategoryManagement.layout = {
    breadcrumbs: [
        {
            title: 'Issues',
            href: '/issues',
        },
        {
            title: 'Categories',
            href: '/categories',
        },
    ],
};
