import { Form, Link } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { MultiSelect } from '@/components/ui/multi-select';
import { Spinner } from '@/components/ui/spinner';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Category, IssueFormData } from '@/types/issues';
import { toast } from 'sonner';

type Props = {
    issue?: IssueFormData;
    categories: Category[];
    priorities: Record<string, string>;
    statuses: Record<string, string>;
    open?: boolean;
    onClose?: () => void;
};

export default function IssueForm({
    issue,
    categories,
    priorities,
    statuses,
    open = true,
    onClose,
}: Props) {
    const isEditing = !!issue;
    const formAction = isEditing
        ? `/issues/${issue.id}`
        : '/issues';

    const [selectedCategories, setSelectedCategories] = useState<(string | number)[]>(
        issue?.category_ids ?? [],
    );

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        {isEditing ? 'Edit Issue' : 'Create New Issue'}
                    </DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? 'Update the issue details below.'
                            : 'Fill in the details to create a new issue.'}
                    </DialogDescription>
                </DialogHeader>
                <Form
                    key={issue?.id}
                    action={formAction}
                    method={isEditing ? 'put' : 'post'}
                    onSuccess={() => {
                        const message = isEditing
                            ? 'Issue updated successfully'
                            : 'Issue created successfully';
                        toast.success(message);
                        onClose?.();
                    }}
                    className="flex flex-col gap-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="title">
                                        Title <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="title"
                                        name="title"
                                        required
                                        autoFocus
                                        defaultValue={issue?.title ?? ''}
                                    />
                                    <InputError message={errors.title} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="description">
                                        Description{' '}
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <textarea
                                        id="description"
                                        name="description"
                                        required
                                        rows={4}
                                        defaultValue={issue?.description ?? ''}
                                        className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                    <InputError message={errors.description} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="status">
                                        Status <span className="text-red-500">*</span>
                                    </Label>
                                    <Select
                                        name="status"
                                        defaultValue={issue?.status ?? Object.keys(statuses)[0]}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent align="start">
                                            {Object.entries(statuses).map(
                                                ([value, label]) => (
                                                    <SelectItem
                                                        key={value}
                                                        value={value}
                                                    >
                                                        {label}
                                                    </SelectItem>
                                                ),
                                            )}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.status} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="priority">Priority</Label>
                                    <Select
                                        name="priority"
                                        defaultValue={issue?.priority ?? ''}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select priority" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {Object.entries(priorities).map(
                                                ([value, label]) => (
                                                    <SelectItem
                                                        key={value}
                                                        value={value}
                                                    >
                                                        {label}
                                                    </SelectItem>
                                                ),
                                            )}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.priority} />
                                </div>

                                <div className="grid gap-2">
                                    <Label>Categories</Label>
                                    <MultiSelect
                                        options={categories.map((cat) => ({
                                            label: cat.name,
                                            value: cat.id,
                                        }))}
                                        selected={selectedCategories}
                                        onChange={setSelectedCategories}
                                        placeholder="Select categories..."
                                        emptyMessage={
                                            <span>
                                                No categories available yet.{' '}
                                                <Link
                                                    href="/categories"
                                                    className="text-primary underline hover:no-underline"
                                                >
                                                    Create one here
                                                </Link>
                                                .
                                            </span>
                                        }
                                    />
                                    {selectedCategories.map((categoryId) => (
                                        <input
                                            key={categoryId}
                                            type="hidden"
                                            name="category_ids[]"
                                            value={categoryId}
                                        />
                                    ))}
                                    <InputError message={errors.category_ids} />
                                </div>
                            </div>

                            <DialogFooter>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={onClose}
                                >
                                    Close
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing && <Spinner className="mr-2" />}
                                    {isEditing ? 'Update Issue' : 'Create Issue'}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
