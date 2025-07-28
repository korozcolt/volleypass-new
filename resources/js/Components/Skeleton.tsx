interface SkeletonProps {
    className?: string;
    variant?: 'text' | 'circular' | 'rectangular' | 'card';
    width?: string;
    height?: string;
    lines?: number;
}

export default function Skeleton({ 
    className = '', 
    variant = 'rectangular', 
    width = 'w-full', 
    height = 'h-4', 
    lines = 1 
}: SkeletonProps) {
    const baseClasses = 'animate-pulse bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 bg-[length:200%_100%]';
    
    const variantClasses = {
        text: 'rounded',
        circular: 'rounded-full',
        rectangular: 'rounded-md',
        card: 'rounded-lg'
    };

    if (variant === 'text' && lines > 1) {
        return (
            <div className={`space-y-2 ${className}`}>
                {Array.from({ length: lines }).map((_, index) => (
                    <div
                        key={index}
                        className={`${baseClasses} ${variantClasses[variant]} ${width} ${height} ${
                            index === lines - 1 ? 'w-3/4' : ''
                        }`}
                    />
                ))}
            </div>
        );
    }

    return (
        <div
            className={`${baseClasses} ${variantClasses[variant]} ${width} ${height} ${className}`}
        />
    );
}

// Componentes específicos para diferentes tipos de contenido
export function UserSkeleton() {
    return (
        <div className="flex items-center space-x-4 p-4 bg-gradient-to-r from-slate-800/50 to-purple-800/50 rounded-lg border border-purple-500/20">
            <Skeleton variant="circular" width="w-12" height="h-12" />
            <div className="flex-1">
                <Skeleton variant="text" width="w-32" height="h-4" className="mb-2" />
                <Skeleton variant="text" width="w-24" height="h-3" />
            </div>
        </div>
    );
}

export function MatchSkeleton() {
    return (
        <div className="bg-gradient-to-r from-slate-800/50 to-purple-800/50 rounded-lg border border-purple-500/20 p-6">
            <div className="flex justify-between items-center mb-4">
                <Skeleton variant="text" width="w-20" height="h-4" />
                <Skeleton variant="rectangular" width="w-16" height="h-6" className="rounded-full" />
            </div>
            
            <div className="flex items-center justify-between mb-4">
                <div className="flex items-center space-x-3">
                    <Skeleton variant="circular" width="w-10" height="h-10" />
                    <Skeleton variant="text" width="w-24" height="h-5" />
                </div>
                
                <div className="text-center">
                    <Skeleton variant="text" width="w-12" height="h-8" className="mb-1" />
                    <Skeleton variant="text" width="w-16" height="h-3" />
                </div>
                
                <div className="flex items-center space-x-3">
                    <Skeleton variant="text" width="w-24" height="h-5" />
                    <Skeleton variant="circular" width="w-10" height="h-10" />
                </div>
            </div>
            
            <div className="flex justify-between text-sm">
                <Skeleton variant="text" width="w-20" height="h-4" />
                <Skeleton variant="text" width="w-16" height="h-4" />
            </div>
        </div>
    );
}

export function StatsSkeleton() {
    return (
        <div className="bg-gradient-to-r from-slate-800/50 to-purple-800/50 rounded-lg border border-purple-500/20 p-6">
            <div className="flex items-center justify-between mb-4">
                <Skeleton variant="circular" width="w-8" height="h-8" />
                <Skeleton variant="text" width="w-16" height="h-4" />
            </div>
            <Skeleton variant="text" width="w-20" height="h-8" className="mb-2" />
            <Skeleton variant="text" width="w-32" height="h-4" />
        </div>
    );
}

export function DashboardCardSkeleton() {
    return (
        <div className="bg-gradient-to-br from-slate-800/50 to-purple-800/50 rounded-xl border border-purple-500/20 p-6">
            <div className="flex items-center justify-between mb-4">
                <div className="flex items-center space-x-3">
                    <Skeleton variant="circular" width="w-12" height="h-12" />
                    <div>
                        <Skeleton variant="text" width="w-24" height="h-5" className="mb-2" />
                        <Skeleton variant="text" width="w-16" height="h-4" />
                    </div>
                </div>
                <Skeleton variant="rectangular" width="w-6" height="h-6" />
            </div>
            
            <Skeleton variant="text" width="w-16" height="h-8" className="mb-2" />
            <Skeleton variant="text" width="w-32" height="h-4" />
        </div>
    );
}

export function ProfileSkeleton() {
    return (
        <div className="bg-gradient-to-r from-slate-800/50 to-purple-800/50 rounded-lg border border-purple-500/20 p-6">
            <div className="flex items-center space-x-6 mb-6">
                <Skeleton variant="circular" width="w-20" height="h-20" />
                <div className="flex-1">
                    <Skeleton variant="text" width="w-40" height="h-6" className="mb-2" />
                    <Skeleton variant="text" width="w-24" height="h-4" className="mb-2" />
                    <Skeleton variant="text" width="w-32" height="h-4" />
                </div>
            </div>
            
            <div className="space-y-4">
                <div>
                    <Skeleton variant="text" width="w-20" height="h-4" className="mb-2" />
                    <Skeleton variant="rectangular" width="w-full" height="h-10" />
                </div>
                <div>
                    <Skeleton variant="text" width="w-16" height="h-4" className="mb-2" />
                    <Skeleton variant="rectangular" width="w-full" height="h-10" />
                </div>
                <div>
                    <Skeleton variant="text" width="w-24" height="h-4" className="mb-2" />
                    <Skeleton variant="rectangular" width="w-full" height="h-10" />
                </div>
            </div>
        </div>
    );
}