import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type Category = {
    id: number;
    name: string;
};

type CategoryFilterDropdownProps = {
    categoryFilter: string;
    categories: Category[];
    onFilterChange: (value: string) => void;
};

export default function CategoryFilterDropdown({
    categoryFilter,
    categories,
    onFilterChange,
}: CategoryFilterDropdownProps) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" size="sm">
                    Category: {categoryFilter === 'all' ? 'All' : categories.find(c => c.id === parseInt(categoryFilter))?.name}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem onClick={() => onFilterChange('all')}>
                    All
                </DropdownMenuItem>
                {categories.map((category) => (
                    <DropdownMenuItem
                        key={category.id}
                        onClick={() => onFilterChange(category.id.toString())}
                    >
                        {category.name}
                    </DropdownMenuItem>
                ))}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
