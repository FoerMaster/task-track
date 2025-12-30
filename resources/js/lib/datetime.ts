export function formatDate(dateString: string) {
    const date = new Date(dateString);
    const now = new Date();

    if (
        date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear()
    ) {
        return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    }

    if (date.getFullYear() === now.getFullYear()) {
        return date.toLocaleDateString([], { day: "numeric", month: "short" });
    }

    return date.toLocaleDateString([], { day: "numeric", month: "short", year: "numeric" });
}
