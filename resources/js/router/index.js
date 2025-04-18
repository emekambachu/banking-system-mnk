import {
    createRouter,
    createWebHistory,
} from 'vue-router'
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import axios from 'axios';

let user = null;

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: () => import('@/js/layouts/AuthLayout.vue'),

            children: [
                {
                    path: '/',
                    name: 'register',
                    component: () => import('@/js/pages/auth/RegisterView.vue'),
                },
                {
                    path: 'login',
                    name: 'login',
                    component: () => import('@/js/pages/auth/LoginView.vue'),
                },

            ],
        },

        {
            path: '/account',
            component: () => import('@/js/layouts/AccountLayout.vue'),
            meta: { requiresAuth: true },

            children: [
                {
                    path: 'dashboard',
                    name: 'dashboard',
                    component: () => import('@/js/pages/account/DashboardView.vue'),
                },
                {
                    path: 'users',
                    name: 'users',
                    component: () => import('@/js/pages/account/users/UserList.vue'),
                    props: true,
                    meta: { requiresAdmin: true },
                },
                {
                    path: 'my-transactions',
                    name: 'my-transactions',
                    component: () => import('@/js/pages/account/transactions/TransactionList.vue'),
                    props: true
                },
                {
                    path: 'transactions/:id',
                    name: 'transactions',
                    component: () => import('@/js/pages/account/transactions/TransactionList.vue'),
                    props: true,
                    meta: { requiresAdmin: true },
                },
                {
                    path: 'funds-transfer',
                    name: 'funds-transfer',
                    component: () => import('@/js/pages/account/funds-transfer/FundsTransferView.vue'),
                },

                {
                    path: 'un-authorized',
                    name: 'unauthorized',
                    component: () => import('@/js/pages/account/UnauthorizedView.vue'),
                },

            ],
        },

    ],
});

// Helper function to check authentication.
const authenticateUser = async () => {
    try {
        await axios.get(`/sanctum/csrf-cookie`);
        const response = await apiClient.get('/authenticate');
        if(response.data.success){
            user = response.data.user;
            return true;
        } else {
            handleErrors.hideErrorInProduction('Un-authorized', response.data);
            return false;
        }
    } catch (error) {
        handleErrors.hideErrorInProduction('Auth Error', error.response);
        return false;
    }
};

// Global navigation guard
router.beforeEach(async (to, from, next) => {
    try {
        if (to.meta.requiresAuth) {
            const isAuthenticated = await authenticateUser();
            if (!isAuthenticated || user === null) {
                return next({ name: 'login' });
            }
            if (to.meta.requiresAdmin && !user?.roles?.includes('admin')) {
                return next({ name: 'unauthorized' });
            }
        }
        // Scroll to top on route change
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
        next();
    } catch (err) {
        return next({ name: 'login' });
    }
});

export default router
