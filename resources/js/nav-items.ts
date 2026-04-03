import { Book, ClipboardList, LayoutGrid } from 'lucide-react';
import { dashboard } from '@/routes';
import { index as issues } from '@/routes/issues';
import type { NavItem } from '@/types';

export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Issues',
        href: issues.url(),
        icon: ClipboardList,
    },
    {
        title: 'API Documentation',
        href: '/docs/api',
        icon: Book,
        external: true,
    },
];
