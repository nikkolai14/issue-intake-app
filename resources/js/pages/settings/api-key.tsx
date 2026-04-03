import { Transition } from '@headlessui/react';
import { Form, Head } from '@inertiajs/react';
import ApiKeyController from '@/actions/App/Http/Controllers/Settings/ApiKeyController';
import Heading from '@/components/heading';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index } from '@/routes/api-key';

function NewApiKeyAlert({ apiKey }: { apiKey: string }) {
    const handleCopyKey = () => {
        navigator.clipboard.writeText(apiKey);
    };

    return (
        <Alert className="border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950">
            <AlertDescription className="w-full">
                <p className="mb-2 font-medium">
                    Your new API key has been generated. Make sure to copy it
                    now as you won't be able to see it again.
                </p>
                <div className="flex w-full items-center gap-2">
                    <code className="min-w-0 flex-1 overflow-x-auto break-all rounded bg-neutral-100 p-2 font-mono text-sm dark:bg-neutral-800">
                        {apiKey}
                    </code>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={handleCopyKey}
                        className="shrink-0"
                    >
                        Copy
                    </Button>
                </div>
            </AlertDescription>
        </Alert>
    );
}

function ExistingApiKey({
    apiKey,
}: {
    apiKey: {
        id: number;
        name: string;
        key: string;
        last_used_at: string | null;
        created_at: string;
    };
}) {
    return (
        <Card className="p-6">
            <div className="space-y-4">
                <div>
                    <Label className="text-sm font-medium">
                        API Key Name
                    </Label>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {apiKey.name}
                    </p>
                </div>

                <div>
                    <Label className="text-sm font-medium">
                        API Key
                    </Label>
                    <p className="mt-1 font-mono text-sm text-muted-foreground">
                        {apiKey.key}
                    </p>
                </div>

                <div>
                    <Label className="text-sm font-medium">
                        Last Used
                    </Label>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {apiKey.last_used_at ?? 'Never'}
                    </p>
                </div>

                <div>
                    <Label className="text-sm font-medium">
                        Created
                    </Label>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {apiKey.created_at}
                    </p>
                </div>

                <Dialog>
                    <DialogTrigger asChild>
                        <Button variant="destructive">
                            Revoke API Key
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogTitle>
                            Are you sure you want to revoke this API key?
                        </DialogTitle>
                        <DialogDescription>
                            Once your API key is revoked, it will no longer work
                            and any applications using it will lose access. This
                            action cannot be undone.
                        </DialogDescription>

                        <Form {...ApiKeyController.destroy.form()}>
                            {({ processing }) => (
                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button variant="secondary">
                                            Cancel
                                        </Button>
                                    </DialogClose>

                                    <Button
                                        variant="destructive"
                                        disabled={processing}
                                        asChild
                                    >
                                        <button type="submit">
                                            Revoke API Key
                                        </button>
                                    </Button>
                                </DialogFooter>
                            )}
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
        </Card>
    );
}

function GenerateApiKeyForm() {
    return (
        <div className="space-y-4">
            <p className="text-sm text-muted-foreground">
                You don't have an API key yet. Generate one to
                start using the API.
            </p>

            <Form {...ApiKeyController.store.form()}>
                {({ processing, recentlySuccessful }) => (
                    <div className="space-y-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                API Key Name
                            </Label>
                            <Input
                                id="name"
                                name="name"
                                placeholder="My API Key"
                                required
                            />
                        </div>

                        <div className="flex items-center gap-4">
                            <Button disabled={processing}>
                                Generate API Key
                            </Button>

                            <Transition
                                show={recentlySuccessful}
                                enter="transition ease-in-out"
                                enterFrom="opacity-0"
                                leave="transition ease-in-out"
                                leaveTo="opacity-0"
                            >
                                <p className="text-sm text-neutral-600">
                                    Generated
                                </p>
                            </Transition>
                        </div>
                    </div>
                )}
            </Form>
        </div>
    );
}

export default function ApiKey({
    apiKey,
    newApiKey,
}: {
    apiKey?: {
        id: number;
        name: string;
        key: string;
        last_used_at: string | null;
        created_at: string;
    } | null;
    newApiKey?: string | null;
}) {

    return (
        <>
            <Head title="API Key" />

            <h1 className="sr-only">API Key</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="API Key"
                    description="Manage your API key for accessing the API"
                />

                {newApiKey && <NewApiKeyAlert apiKey={newApiKey} />}

                {apiKey ? <ExistingApiKey apiKey={apiKey} /> : <GenerateApiKeyForm />}
            </div>
        </>
    );
}

ApiKey.layout = {
    breadcrumbs: [
        {
            title: 'API Key',
            href: index(),
        },
    ],
};
