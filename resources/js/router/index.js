import {
    createRouter,
    createWebHistory,
} from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: () => import('@/js/layouts/AuthLayout.vue'),

            children: [
                {
                    path: ['/', '/register'],
                    name: 'register',
                    component: () => import('@/js/pages/auth/RegisterView.vue'),
                },
                {
                    path: '/login',
                    name: 'login',
                    component: () => import('@/js/pages/auth/LoginView.vue'),
                },

            ],
        },

        {
            path: '/account',
            component: () => import('@/js/layouts/AccountLayout.vue'),

            children: [
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    component: () => import('@/js/pages/account/DashboardView.vue'),
                },
                {
                    path: '/users',
                    name: 'users',
                    component: () => import('@/js/pages/account/users/UserList.vue'),
                },
                {
                    path: '/transactions',
                    name: 'transactions',
                    component: () => import('@/js/pages/account/transactions/TransactionList.vue'),
                },
                {
                    path: '/funds-transfer',
                    name: 'funds-transfer',
                    component: () => import('@/js/pages/account/funds-transfer/FundsTransferView.vue'),
                },

            ],
        },

    ],
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
