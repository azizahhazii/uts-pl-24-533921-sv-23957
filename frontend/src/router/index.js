import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import ItemsView from '../views/ItemsView.vue';

const routes = [
  { path: '/login', name: 'login', component: LoginView },
  { path: '/items', name: 'items', component: ItemsView },
  { path: '/', redirect: '/items' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Proteksi Route: Jika belum login (tidak ada token), lempar ke /login
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.name !== 'login' && !token) {
    next({ name: 'login' });
  } else {
    next();
  }
});

export default router;