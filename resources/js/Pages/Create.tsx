import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function Create() {
    const { data, setData, post } = useForm({
        url: '',
    });

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post('/short/store');
    };

    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
    const csrfToken = csrfTokenElement ? csrfTokenElement.content : '';


    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Create Shorten URLs
                </h2>
            }
        >
            <Head title="Create" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div className="p-8 text-gray-900">
                            <h2 className="text-2xl font-semibold mb-6">Add New URL</h2>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Insert URL:</label>
                                    <input 
                                        type="text" 
                                        value={data.url} 
                                        onChange={(e) => setData('url', e.target.value)}
                                        className="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                        placeholder="https://example.com" 
                                        required
                                    />
                                </div>
                                <div className="flex justify-end">
                                    <button 
                                        type="submit" 
                                        className="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-200"
                                    >
                                        Create
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
