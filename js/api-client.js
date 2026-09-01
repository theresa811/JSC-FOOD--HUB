/**
 * API Client for Food Hub Backend
 * Handles all communication with PHP backend APIs
 */

const ApiClient = (() => {
    const BASE_URL = 'http://localhost/jsc-food-hub/backend/api';
    let authToken = localStorage.getItem('authToken');

    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`
    };

    // ===== AUTHENTICATION =====
    const auth = {
        login: async (username, password, role) => {
            try {
                const response = await fetch(`${BASE_URL}/auth.php?action=login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password, role })
                });
                const data = await response.json();
                if (data.success) {
                    localStorage.setItem('authToken', data.token);
                    localStorage.setItem('userData', JSON.stringify(data.user));
                    authToken = data.token;
                }
                return data;
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        verify: async () => {
            try {
                const response = await fetch(`${BASE_URL}/auth.php?action=verify`, {
                    method: 'GET',
                    headers
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        logout: () => {
            localStorage.removeItem('authToken');
            localStorage.removeItem('userData');
            authToken = null;
        }
    };

    // ===== MENU ITEMS =====
    const menuItems = {
        getAll: async () => {
            try {
                const response = await fetch(`${BASE_URL}/menu_items.php`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        create: async (menuItem) => {
            try {
                const response = await fetch(`${BASE_URL}/menu_items.php`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(menuItem)
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        update: async (id, menuItem) => {
            try {
                const response = await fetch(`${BASE_URL}/menu_items.php?id=${id}`, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(menuItem)
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        delete: async (id) => {
            try {
                const response = await fetch(`${BASE_URL}/menu_items.php?id=${id}`, {
                    method: 'DELETE',
                    headers
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        }
    };

    // ===== INVENTORY =====
    const inventory = {
        getAll: async () => {
            try {
                const response = await fetch(`${BASE_URL}/inventory.php`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        getLowStock: async () => {
            try {
                const response = await fetch(`${BASE_URL}/inventory.php?low=1`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        add: async (stock) => {
            try {
                const response = await fetch(`${BASE_URL}/inventory.php`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(stock)
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        update: async (id, stock) => {
            try {
                const response = await fetch(`${BASE_URL}/inventory.php?id=${id}`, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(stock)
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        delete: async (id) => {
            try {
                const response = await fetch(`${BASE_URL}/inventory.php?id=${id}`, {
                    method: 'DELETE',
                    headers
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        }
    };

    // ===== SALES =====
    const sales = {
        getAll: async () => {
            try {
                const response = await fetch(`${BASE_URL}/sales.php`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        record: async (saleData) => {
            try {
                const response = await fetch(`${BASE_URL}/sales.php`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(saleData)
                });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        getStats: async () => {
            try {
                const response = await fetch(`${BASE_URL}/sales.php?stats=1`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        }
    };

    // ===== REPORTS =====
    const reports = {
        getInventoryReport: async () => {
            try {
                const response = await fetch(`${BASE_URL}/reports.php?type=inventory`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        getSalesReport: async () => {
            try {
                const response = await fetch(`${BASE_URL}/reports.php?type=sales`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        getAlertsReport: async () => {
            try {
                const response = await fetch(`${BASE_URL}/reports.php?type=alerts`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        },

        getDashboardReport: async () => {
            try {
                const response = await fetch(`${BASE_URL}/reports.php?type=dashboard`, { headers });
                return await response.json();
            } catch (error) {
                return { success: false, message: error.message };
            }
        }
    };

    return {
        auth,
        menuItems,
        inventory,
        sales,
        reports
    };
})();