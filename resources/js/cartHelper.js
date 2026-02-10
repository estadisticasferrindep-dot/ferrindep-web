window.getCartSafe = function () {
    try {
        const raw = localStorage.getItem("cartQunuy");
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        localStorage.removeItem("cartQunuy");
        return [];
    }
};
