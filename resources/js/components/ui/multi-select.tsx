import * as React from 'react';
import { CheckIcon, ChevronsUpDownIcon, XIcon } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';

export type MultiSelectOption = {
    label: string;
    value: string | number;
};

type MultiSelectProps = {
    options: MultiSelectOption[];
    selected: (string | number)[];
    onChange: (selected: (string | number)[]) => void;
    placeholder?: string;
    className?: string;
    disabled?: boolean;
    emptyMessage?: React.ReactNode;
};

export function MultiSelect({
    options,
    selected,
    onChange,
    placeholder = 'Select items...',
    className,
    disabled = false,
    emptyMessage,
}: MultiSelectProps) {
    const [open, setOpen] = React.useState(false);

    const handleSelect = (value: string | number) => {
        const newSelected = selected.includes(value)
            ? selected.filter((item) => item !== value)
            : [...selected, value];
        onChange(newSelected);
    };

    const handleRemove = (value: string | number, e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        onChange(selected.filter((item) => item !== value));
    };

    const selectedOptions = options.filter((option) =>
        selected.includes(option.value),
    );

    if (options.length === 0 && emptyMessage) {
        return <div className="text-sm text-muted-foreground">{emptyMessage}</div>;
    }

    return (
        <DropdownMenu open={open} onOpenChange={setOpen}>
            <DropdownMenuTrigger asChild disabled={disabled}>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    className={cn(
                        'w-full justify-between font-normal',
                        !selectedOptions.length && 'text-muted-foreground',
                        className,
                    )}
                >
                    <div className="flex flex-1 flex-wrap items-center gap-1">
                        {selectedOptions.length === 0 ? (
                            <span>{placeholder}</span>
                        ) : (
                            selectedOptions.map((option) => (
                                <Badge
                                    key={option.value}
                                    variant="secondary"
                                    className="mr-1"
                                >
                                    {option.label}
                                    <span
                                        role="button"
                                        tabIndex={0}
                                        className="ring-offset-background focus:ring-ring ml-1 rounded-full outline-hidden focus:ring-2 focus:ring-offset-2 cursor-pointer"
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') {
                                                handleRemove(option.value, e as any);
                                            }
                                        }}
                                        onMouseDown={(e) => {
                                            e.preventDefault();
                                            e.stopPropagation();
                                        }}
                                        onClick={(e) => handleRemove(option.value, e)}
                                    >
                                        <XIcon className="text-muted-foreground hover:text-foreground size-3" />
                                    </span>
                                </Badge>
                            ))
                        )}
                    </div>
                    <ChevronsUpDownIcon className="ml-2 size-4 shrink-0 opacity-50" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-full p-0" align="start">
                <div className="max-h-64 overflow-auto p-1">
                    {options.map((option) => (
                        <DropdownMenuCheckboxItem
                            key={option.value}
                            checked={selected.includes(option.value)}
                            onCheckedChange={() => handleSelect(option.value)}
                            onSelect={(e) => e.preventDefault()}
                        >
                            {option.label}
                        </DropdownMenuCheckboxItem>
                    ))}
                </div>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
