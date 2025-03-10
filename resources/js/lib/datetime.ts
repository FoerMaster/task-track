export function formatDate(dateString: string) {
    const date = new Date(dateString);
    const now = new Date();

    // Проверяем, является ли дата сегодняшней
    if (
        date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear()
    ) {
        // Возвращаем только время
        return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    }

    // Проверяем, является ли год текущим
    if (date.getFullYear() === now.getFullYear()) {
        // Возвращаем только день и месяц
        return date.toLocaleDateString([], { day: "numeric", month: "short" });
    }

    // Если год не текущий, возвращаем полную дату
    return date.toLocaleDateString([], { day: "numeric", month: "short", year: "numeric" });
}
