import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { PencilIcon, TrashIcon } from 'lucide-react';
import { Issue } from '@/types/issues';

type IssueDetailsDialogProps = {
    issue: Issue | null;
    onClose: () => void;
    onEdit: (id: number) => void;
    onDelete: (id: number) => void;
};

export default function IssueDetailsDialog({
    issue,
    onClose,
    onEdit,
    onDelete
}: IssueDetailsDialogProps) {
    if (!issue) return null;

    return (
        <Dialog open={true} onOpenChange={onClose}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{issue.title}</DialogTitle>
                </DialogHeader>
                <div className="space-y-4">
                    <div>
                        <h4 className="font-semibold">Description</h4>
                        <p className="text-sm text-muted-foreground">{issue.description}</p>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <h4 className="font-semibold">Status</h4>
                            <Badge variant={
                                issue.status?.value === 'completed' ? 'success' :
                                issue.status?.value === 'in_progress' ? 'warning' :
                                issue.status?.value === 'in_review' ? 'info' : 'default'
                            }>
                                {issue.status?.label || 'Todo'}
                            </Badge>
                        </div>
                        <div>
                            <h4 className="font-semibold">Priority</h4>
                            <Badge variant={
                                issue.priority?.value === 'urgent' ? 'destructive' :
                                issue.priority?.value === 'high' ? 'default' :
                                issue.priority?.value === 'medium' ? 'secondary' : 'outline'
                            }>
                                {issue.priority?.label || 'None'}
                            </Badge>
                        </div>
                    </div>
                    <div>
                        <h4 className="font-semibold">Categories</h4>
                        <div className="flex flex-wrap gap-1">
                            {issue.categories.length > 0 ? (
                                issue.categories.map((category) => (
                                    <Badge key={category.id} variant="outline">
                                        {category.name}
                                    </Badge>
                                ))
                            ) : (
                                <span className="text-sm text-muted-foreground">No categories</span>
                            )}
                        </div>
                    </div>
                    <div className="flex justify-end gap-2 pt-4">
                        <Button
                            variant="outline"
                            onClick={() => onEdit(issue.id)}
                        >
                            <PencilIcon className="size-4 mr-2" />
                            Edit
                        </Button>
                        <Button
                            variant="destructive"
                            onClick={() => onDelete(issue.id)}
                        >
                            <TrashIcon className="size-4 mr-2" />
                            Delete
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}