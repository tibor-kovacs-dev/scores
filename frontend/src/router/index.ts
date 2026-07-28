import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/match/:id',
      name: 'match-detail',
      component: () => import('../views/MatchDetail.vue'),
      props: true,
    },
    {
      path: '/team/:id',
      name: 'team-detail',
      component: () => import('../views/TeamDetail.vue'),
      props: true,
    },
  ],
})

export default router