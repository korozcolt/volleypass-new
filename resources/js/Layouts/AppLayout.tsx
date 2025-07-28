import { ReactNode } from 'react';
import { Head } from '@inertiajs/react';
import Navigation from '@/Components/Navigation';
import { User } from '@/types/global';

interface AppLayoutProps {
    children: ReactNode;
    title?: string;
    user?: User;
}

export default function AppLayout({ children, title = 'VolleyPass', user }: AppLayoutProps) {
    return (
        <>
            <Head title={title} />
            <div className="min-h-screen bg-gray-100">
                <Navigation user={user} />
                <main>
                    {children}
                </main>
            </div>
        </>
    );
}