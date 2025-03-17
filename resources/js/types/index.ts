import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    assigned_projects: Pick<Project, 'id'|'name'|'code_name'>[];
    users_list: User[];
    projects: Project[];
    statuses: IdNameObject[];
    task_types: IdNameObject[];
}

export interface IdNameObject {
    id: number;
    name: string;
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
    task_id: number;
    file_name: string;
    attachment_url: string;
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
    name: string;
    code_name: string;
    participants: {
        user: Pick<User, 'id' | 'name' | 'email' | 'full_name'>;
        role: {
            id: number;
            name: string;
        };
    }[];
}

interface TaskComment {
    id: number;
    task_id: number;
    user_id: number;
    comment: string;
    created_at: string;
}

export interface Task {
    id: number;
    name: string;
    description: string;
    project_id: number;
    status: number;
    task_type: number;
    updated_from?: any;
    create_from: number;
    deadline: string;
    created_at: string;
    updated_at: string;
    responsibles: number[];
    executors: number[];
    comments: TaskComment[];
    attachments: Attachment[];
}

export type BreadcrumbItemType = BreadcrumbItem;
