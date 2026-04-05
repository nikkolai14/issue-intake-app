import { Info } from 'lucide-react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

interface TruncatedTextTooltipProps {
    text: string;
    maxLength?: number;
    className?: string;
    title?: string;
}

export default function TruncatedTextTooltip({ text, maxLength = 50, className = '', title = 'Full Content' }: TruncatedTextTooltipProps) {
    const truncateText = (content: string, limit: number) => {
        if (content.length <= limit) return { text: content, isTruncated: false };
        return { text: content.substring(0, limit) + '...', isTruncated: true };
    };

    const { text: truncatedText, isTruncated } = truncateText(text, maxLength);

    return (
        <div className={`flex items-center gap-1 ${className}`}>
            <span>{truncatedText}</span>
            {isTruncated && (
                <Dialog>
                    <DialogTrigger asChild>
                        <Info className="size-3 text-muted-foreground cursor-pointer flex-shrink-0 hover:text-foreground transition-colors" />
                    </DialogTrigger>
                    <DialogContent className="max-w-md">
                        <DialogHeader>
                            <DialogTitle>{title}</DialogTitle>
                        </DialogHeader>
                        <div className="mt-4">
                            <div className="text-sm whitespace-pre-wrap leading-relaxed bg-muted/50 p-4 rounded-md">
                                {text}
                            </div>
                        </div>
                    </DialogContent>
                </Dialog>
            )}
        </div>
    );
}