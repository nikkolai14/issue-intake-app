import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type PriorityFilterDropdownProps = {
    priorityFilter: string;
    priorities: Record<string, string>;
    onFilterChange: (value: string) => void;
};

export default function PriorityFilterDropdown({
    priorityFilter,
    priorities,
    onFilterChange,
}: PriorityFilterDropdownProps) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" size="sm">
                    Priority: {priorityFilter === 'all' ? 'All' : priorities[priorityFilter]}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem onClick={() => onFilterChange('all')}>
                    All
                </DropdownMenuItem>
                {Object.entries(priorities).map(([value, label]) => (
                    <DropdownMenuItem
                        key={value}
                        onClick={() => onFilterChange(value)}
                    >
                        {label}
                    </DropdownMenuItem>
                ))}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
