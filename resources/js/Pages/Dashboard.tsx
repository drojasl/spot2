import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { FaTrash } from 'react-icons/fa';
import { FaCopy } from 'react-icons/fa';

interface Url {
    id: number;
    url: string;
    shortened: string;
}

interface DashboardProps {
    urls: {
        data: Url[];
        prev_page_url?: string | null;
        next_page_url?: string | null;
    };
}

export default function Dashboard({ urls }: DashboardProps) {
    const handleDelete = async (id: number) => {
        if (confirm('Are you sure you want to delete this URL?')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }
            
            await fetch(`/short/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
            });

            window.location.reload();
        }
    };

    const handleCopy = (shortened: string) => {
        const appUrl = import.meta.env.VITE_APP_URL
        navigator.clipboard.writeText(appUrl+"/go/"+shortened);
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    List Shortened URLs
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-lg font-semibold mb-4">Your Shortened URLs</h3>
                            <table className="min-w-full bg-white shadow-md rounded-lg">
                                <thead>
                                    <tr>
                                        <th className="py-2 px-4 bg-gray-100 text-left">Original URL</th>
                                        <th className="py-2 px-4 bg-gray-100 text-left">Shortened URL</th>
                                        <th className="py-2 px-4 bg-gray-100 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {urls && urls.data.length > 0 ? (
                                        urls.data.map((url) => (
                                            <tr key={url.id}>
                                                <td className="py-2 px-4 border-b">
                                                    <a 
                                                        href={url.url} 
                                                        target="_blank" 
                                                        rel="noopener noreferrer" 
                                                        className="text-blue-500 hover:underline"
                                                    >
                                                        {url.url}
                                                    </a>
                                                </td>
                                                <td className="py-2 px-4 border-b">
                                                    <span>{url.shortened}</span>
                                                </td>
                                                <td className="py-2 px-4 border-b">
                                                    <button 
                                                        onClick={() => handleCopy(url.shortened)} 
                                                        className="mr-2 text-blue-600 hover:text-blue-800"
                                                    >
                                                        <FaCopy className="inline-block mr-1" />
                                                    </button>
                                                    <button 
                                                        onClick={() => handleDelete(url.id)} 
                                                        className="text-red-600 hover:text-red-800"
                                                    >
                                                        <FaTrash className="inline-block ml-2" />
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td className="py-2 px-4" colSpan={3}>
                                                No URLs found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                            <div className="mt-4">
                                {urls.prev_page_url && (
                                    <Link href={urls.prev_page_url} className="px-4 py-2 text-gray-700 bg-gray-200 rounded-md">
                                        Previous
                                    </Link>
                                )}
                                {urls.next_page_url && (
                                    <Link href={urls.next_page_url} className="ml-2 px-4 py-2 text-gray-700 bg-gray-200 rounded-md">
                                        Next
                                    </Link>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
