import { Form } from '@inertiajs/react';
import { toast } from 'sonner';
import { Category } from '@/types/issues';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

type Props = {
    category: Category | null;
    onClose: () => void;
};

export default function EditCategoryDialog({ category, onClose }: Props) {
    if (!category) {
        return null;
    }

    return (
        <Dialog open={!!category} onOpenChange={onClose}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Category</DialogTitle>
                    <DialogDescription>
                        Update the category name.
                    </DialogDescription>
                </DialogHeader>
                <Form
                    key={category.id}
                    action={`/categories/${category.id}`}
                    method="put"
                    onSuccess={() => {
                        toast.success('Category updated successfully');
                        onClose();
                    }}
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2 mb-4">
                                <Label htmlFor="edit-name">Name</Label>
                                <Input
                                    id="edit-name"
                                    name="name"
                                    defaultValue={category.name}
                                />
                                <InputError message={errors?.name} />
                            </div>
                            <DialogFooter>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={onClose}
                                >
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {processing && <Spinner className="mr-2" />}
                                    Update
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
