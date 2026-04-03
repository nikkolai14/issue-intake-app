import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { router } from '@inertiajs/react';
import { SettingsIcon } from 'lucide-react';

type SettingsDropdownProps = {
    onCategoriesClick: () => void;
};

export default function SettingsDropdown({
    onCategoriesClick,
}: SettingsDropdownProps) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="outline" size="sm">
                    <SettingsIcon className="mr-2 size-4" />
                    Settings
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem
                    onClick={() =>
                        router.visit('/categories', {
                            onSuccess: onCategoriesClick,
                        })
                    }
                >
                    Categories
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
