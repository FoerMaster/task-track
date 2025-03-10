import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
}

export interface User {
    id: number;
    name: string;
    full_name: string;
    email: string;
    avatar?: string;
    timezone: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Attachment {
    id: number;
    fileName: string;
    extension: string;
    size: number;
    directLink: string;
}

export interface Tag {
    color: number;
    id: number;
    name: string;
}

export interface State {
    id: number;
    name: string;
}

export interface Type {
    id: number;
    name: string;
    color: string;
}

export interface Project {
    id: number;
    description: string;
    icon?: string;
    name: string;
    shortName: string;
    leader: User;
}

export interface Task {
    id: number;
    summary: string;
    description: string;
    attachments: Attachment[];
    initiator: User;
    updater?: User;
    assigned: User[];
    tags: Tag[];
    state: State;
    type: Type;
    project: Project;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
