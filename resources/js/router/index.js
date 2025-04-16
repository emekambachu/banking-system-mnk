import {
    createRouter,
    createWebHistory,
} from 'vue-router'
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import axios from 'axios';

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
                },
                {
                    path: 'transactions',
                    name: 'transactions',
                    component: () => import('@/js/pages/account/transactions/TransactionList.vue'),
                },
                {
                    path: 'funds-transfer',
                    name: 'funds-transfer',
                    component: () => import('@/js/pages/account/funds-transfer/FundsTransferView.vue'),
                },

            ],
        },

    ],
});

// Helper function to check authentication.
const authenticateUser = async () => {
    try {
        await axios.get(`${window.location.origin}/sanctum/csrf-cookie`);
        const response = await apiClient.get('/authenticate');
        if(response.data.success){
            return true;
        } else {
            window.location.href = '/login';
        }
    } catch (error) {
        handleErrors.hideErrorInProduction('Auth Error', error.response);
        window.location.href = '/login';
    }
};

// Global navigation guard
router.beforeEach(async (to, from, next) => {
    if (to.meta.requiresAuth) {
        try {
            await authenticateUser();
            next();
        } catch (err) {
            // Redirect to the login page if not authenticated
            next({ name: 'Login' });
        }
    } else {
        next();
    }
});

router.beforeEach((to, from, next) => {
    // scroll to top on route change
    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    })
    next()
})

export default router
