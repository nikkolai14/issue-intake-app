import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
} from '@/components/ui/dialog';

type DeleteIssueDialogProps = {
    deleteConfirmId: number | null;
    issueTitle?: string;
    onCancel: () => void;
    onConfirm: (id: number) => void;
};

export default function DeleteIssueDialog({
    deleteConfirmId,
    issueTitle,
    onCancel,
    onConfirm,
}: DeleteIssueDialogProps) {
    const truncatedTitle = issueTitle
        ? `${issueTitle.substring(0, 20)}${issueTitle.length > 20 ? '...' : ''}`
        : null;

    return (
        <Dialog open={!!deleteConfirmId} onOpenChange={onCancel}>
            <DialogContent>
                <div className="space-y-4">
                    <div>
                        <h2 className="text-lg font-semibold">
                            Confirm Deletion
                        </h2>
                        <p className="text-muted-foreground text-sm">
                            Are you sure you want to delete
                            {truncatedTitle && (
                                <>
                                    {' '}<strong>"{truncatedTitle}"</strong>
                                </>
                            )}? This action cannot be undone.
                        </p>
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="outline" onClick={onCancel}>
                            Cancel
                        </Button>
                        <Button
                            variant="destructive"
                            onClick={() =>
                                deleteConfirmId && onConfirm(deleteConfirmId)
                            }
                        >
                            Delete
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
