import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { Project, SharedData, User } from '@/types';
import { usePage } from '@inertiajs/vue3';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function getLocalTimeZone(): string {
    return Intl.DateTimeFormat().resolvedOptions().timeZone
}

export function getProjectById(id: number): Project | undefined {
    const page = usePage<SharedData>();
    return page.props.auth.projects.find((project)=> project.id == id)
}


export function getUserById(id: number): User | undefined {
    const page = usePage<SharedData>();
    return page.props.auth.users_list.find((user)=> user.id == id)
}
