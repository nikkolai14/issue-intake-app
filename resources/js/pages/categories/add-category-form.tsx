import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { toast } from 'sonner';
import { PlusIcon } from 'lucide-react';

export default function AddCategoryForm() {
    const [isFormVisible, setIsFormVisible] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/categories', {
            onSuccess: () => {
                reset();
                setIsFormVisible(false);
                toast.success('Category deleted successfully');
            },
        });
    };

    const handleCancel = () => {
        reset();
        setIsFormVisible(false);
    };

    return (
        <div>
            {!isFormVisible ? (
                <Button onClick={() => setIsFormVisible(true)}>
                    <PlusIcon className="size-4" />
                    Add Category
                </Button>
            ) : (
                <form onSubmit={handleSubmit} className="flex gap-2">
                    <div className="flex-1">
                        <Input
                            name="name"
                            placeholder="Category name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                        />
                        <InputError message={errors?.name} />
                    </div>
                    <Button type="submit" disabled={processing}>
                        {processing && <Spinner className="mr-2" />}
                        Add
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={handleCancel}
                        disabled={processing}
                    >
                        Cancel
                    </Button>
                </form>
            )}
        </div>
    );
}
