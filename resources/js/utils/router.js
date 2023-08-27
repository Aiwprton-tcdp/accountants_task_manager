import { createRouter, createWebHistory } from 'vue-router'
import { defineAsyncComponent } from 'vue'

const routes = [{
  path: '/upcoming_payments',
  name: 'upcoming_payments',
  component: defineAsyncComponent(() => import('@pages/UpcomingPayments.vue')),
  // props: route => ({ id: route.query.id }),
  alias: ['/'],
}, {
  path: '/departments',
  name: 'departments',
  component: defineAsyncComponent(() => import('@pages/Departments.vue')),
  // props: route => ({ id: route.query.id }),
}, {
  path: '/:id',
  name: 'department',
  props: true,
  component: defineAsyncComponent(() => import('@pages/Department.vue')),
}, {
  path: '/users',
  name: 'users',
  component: defineAsyncComponent(() => import('@pages/Users.vue')),
}, {
  path: '/llc',
  name: 'llc',
  component: defineAsyncComponent(() => import('@pages/LLC.vue')),
}, {
  path: '/:pathMatch(.*)',
  redirect: '/',
}]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
