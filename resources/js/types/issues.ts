export type Category = {
    id: number;
    name: string;
    created_at?: string;
};

type EnumValue = {
    value: string;
    label: string;
};

type IssueBase = {
    id: number;
    title: string;
    description: string;
    priority: string | null;
    status: string | null;
};

export type Issue = Omit<IssueBase, 'priority' | 'status'> & {
    priority: EnumValue;
    status: EnumValue;
    categories: Category[];
    created_at: string;
    summary: string | null;
    next_action: string | null;
};
        
export type IssueFormData = IssueBase & {
    category_ids: number[];
}

export type Enums = {
    categories: Category[];
    priorities: Record<string, string>;
    statuses: Record<string, string>;
};