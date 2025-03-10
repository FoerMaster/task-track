import { Task } from '@/types';

export const tasks: Task[] = [
    {
        "id": 1,
        "summary": "Разработать главную страницу",
        "description": "Создать адаптивный дизайн для главной страницы проекта",
        "state": {
            id: 1,
            name: "Blocked",
        },
        "type": {
          id: 1,
          name: "Задача",
          color: "#65e165"
        },
        "attachments": [
            {
                "id": 1,
                "fileName": "design-specs",
                "extension": "pdf",
                "size": 254123,
                "directLink": "/files/design-specs.pdf"
            },
            {
                "id": 1,
                "fileName": "design-specs",
                "extension": "pdf",
                "size": 254123,
                "directLink": "/files/design-specs.pdf"
            },
            {
                "id": 1,
                "fileName": "design-specs",
                "extension": "pdf",
                "size": 254123,
                "directLink": "/files/design-specs.pdf"
            }
        ],
        "initiator": {
            "id": 1,
            "name": "ivanov",
            "fullName": "Иван Иванов",
            "email": "ivanov@example.com",
            "avatar": "/avatars/ivanov.jpg",
            "timezone": "Europe/Moscow",
            "email_verified_at": "2024-01-15T08:00:00Z",
            "created_at": "2024-01-01T10:00:00Z",
            "updated_at": "2024-01-15T08:30:00Z"
        },
        "updater": {
            "id": 2,
            "name": "petrova",
            "fullName": "Мария Петрова",
            "email": "petrova@example.com",
            "timezone": "Europe/London",
            "email_verified_at": null,
            "created_at": "2024-01-02T09:00:00Z",
            "updated_at": "2024-01-16T12:45:00Z"
        },
        "assigned": [
            {
                "id": 2,
                "name": "petrova",
                "fullName": "Мария Петрова",
                "email": "petrova@example.com",
                "timezone": "Europe/London",
                "email_verified_at": null,
                "created_at": "2024-01-02T09:00:00Z",
                "updated_at": "2024-01-16T12:45:00Z"
            }
        ],
        "tags": [
            {
                "color": 16711680,
                "id": 3,
                "name": "Фронтенд"
            },
            {
                "color": 65280,
                "id": 1,
                "name": "Важно"
            }
        ],
        "project": {
            "id": 101,
            "description": "Основной продукт компании",
            "icon": "🚀",
            "name": "Web Platform",
            "shortName": "WEB",
            "leader": {
                "id": 1,
                "name": "ivanov",
                "fullName": "Иван Иванов",
                "email": "ivanov@example.com",
                "avatar": "/avatars/ivanov.jpg",
                "timezone": "Europe/Moscow",
                "email_verified_at": "2024-01-15T08:00:00Z",
                "created_at": "2024-01-01T10:00:00Z",
                "updated_at": "2024-01-15T08:30:00Z"
            }
        },
        "created_at": "2024-01-01T10:00:00Z",
        "updated_at": "2024-01-15T08:30:00Z"
    },
    {
        "id": 2,
        "summary": "Исправить баг авторизации",
        "description": "При логине возникает 500 ошибка при пустом поле пароля",
        "state": {
            id: 1,
            name: "Blocked",
        },
        "type": {
            id: 2,
            name: "Баг",
            color: "red"
        },
        "attachments": [
            {
                "id": 2,
                "fileName": "error-log",
                "extension": "txt",
                "size": 1024,
                "directLink": "/files/error-log-0124.txt"
            }
        ],
        "initiator": {
            "id": 3,
            "name": "sidorov",
            "fullName": "Алексей Сидоров",
            "email": "sidorov@example.com",
            "timezone": "Asia/Tokyo",
            "email_verified_at": "2024-02-20T03:00:00Z",
            "created_at": "2024-02-01T07:00:00Z",
            "updated_at": "2024-02-20T03:15:00Z"
        },
        "updater": undefined,
        "assigned": [
            {
                "id": 1,
                "name": "ivanov",
                "fullName": "Иван Иванов",
                "email": "ivanov@example.com",
                "avatar": "/avatars/ivanov.jpg",
                "timezone": "Europe/Moscow",
                "email_verified_at": "2024-01-15T08:00:00Z",
                "created_at": "2024-01-01T10:00:00Z",
                "updated_at": "2024-01-15T08:30:00Z"
            },{
                "id": 1,
                "name": "ivanov",
                "fullName": "Иван Иванов",
                "email": "ivanov@example.com",
                "avatar": "/avatars/ivanov.jpg",
                "timezone": "Europe/Moscow",
                "email_verified_at": "2024-01-15T08:00:00Z",
                "created_at": "2024-01-01T10:00:00Z",
                "updated_at": "2024-01-15T08:30:00Z"
            }
        ],
        "tags": [
            {
                "color": 16711680,
                "id": 2,
                "name": "Ошибка"
            }
        ],
        "project": {
            "id": 102,
            "description": "Мобильное приложение",
            "name": "Mobile App",
            "shortName": "APP",
            "leader": {
                "id": 3,
                "name": "sidorov",
                "fullName": "Алексей Сидоров",
                "email": "sidorov@example.com",
                "timezone": "Asia/Tokyo",
                "email_verified_at": "2024-02-20T03:00:00Z",
                "created_at": "2024-02-01T07:00:00Z",
                "updated_at": "2024-02-20T03:15:00Z"
            }
        },
        "created_at": "2024-01-01T10:00:00Z",
        "updated_at": "2024-01-15T08:30:00Z"
    }
]
