import React, { ReactNode } from 'react';
import { Head } from '@inertiajs/react';
import MainNavigation from '@/Components/Navigation/MainNavigation';
import { User } from '@/types/global';

interface MainLayoutProps {
    children: ReactNode;
    title?: string;
    user?: User;
    currentRoute?: string;
}

export default function MainLayout({ children, title = 'VolleyPass', user, currentRoute }: MainLayoutProps) {
    return (
        <>
            <Head title={title} />
            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800">
                <MainNavigation user={user} currentRoute={currentRoute} />
                <main>
                    {children}
                </main>
            </div>
        </>
    );
}
