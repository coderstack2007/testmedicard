import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    component: () => import('@/pages/auth/LoginPage.vue'),
    meta: { public: true }
  },
  {
    path: '/register',
    component: () => import('@/pages/auth/RegisterPage.vue'),
    meta: { public: true }
  },  
  {
    path: '/',
    component: () => import('@/components/layout/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: 'dashboard',
        component: () => import('@/pages/dashboard/DashboardPage.vue')
      },
      {
        path: 'branches',
        component: () => import('@/pages/branches/BranchListPage.vue')
      },
      {
        path: 'branches/:id',
        component: () => import('@/pages/branches/BranchDetailPage.vue')
      },
      {
        path: 'departments/:branchId',
        component: () => import('@/pages/departments/DepartmentPage.vue')
      },
      {
        path: 'doctors',
        component: () => import('@/pages/doctors/DoctorListPage.vue')
      },
      {
        path: 'patients',
        component: () => import('@/pages/patients/PatientListPage.vue'),
        meta: { roles: ['moderator', 'absolute_admin'] }
      },
      {
        path: 'patients/:id',
        component: () => import('@/pages/patients/PatientDetailPage.vue')
      },
      {
        path: 'patients/:id/diagnoses',
        component: () => import('@/pages/diagnoses/DiagnosisPage.vue')
      },
      {
        path: 'chats',
        component: () => import('@/pages/chat/ChatListPage.vue')
      },
      {
        path: 'chats/:id',
        component: () => import('@/pages/chat/ChatRoomPage.vue')
      }
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/dashboard'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guard
router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Load user from storage on first visit
  if (auth.token && !auth.user) {
    auth.loadFromStorage()
    try {
      await auth.fetchMe()
    } catch (error) {
      auth.logout()
      return '/login'
    }
  }

  // Check authentication requirement
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return '/login'
  }

  // Redirect authenticated users away from public pages
  if (to.meta.public && auth.isAuthenticated) {
    return '/dashboard'
  }

  // Check role-based access
  if (to.meta.roles && !to.meta.roles.includes(auth.user?.role)) {
    return '/dashboard'
  }
})

export default router
