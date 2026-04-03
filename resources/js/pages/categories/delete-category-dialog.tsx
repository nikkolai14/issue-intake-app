import { router } from '@inertiajs/react';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Category } from '@/types/issues';

type Props = {
    category: Category | null;
    onClose: () => void;
};

export default function DeleteCategoryDialog({ category, onClose }: Props) {
    const handleDelete = (id: number) => {
        router.delete(`/categories/${id}`, {
            onSuccess: () => {
                toast.success('Category deleted successfully');
                onClose();
            },
        });
    };

    return (
        <Dialog
            open={!!category}
            onOpenChange={(open) => {
                if (!open) {
                    onClose();
                }
            }}
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Category</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete the category "
                        {category?.name}"? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" onClick={onClose}>
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={() => category && handleDelete(category.id)}
                    >
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
