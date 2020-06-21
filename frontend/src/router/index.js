import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/views/Home'
import List from '@/views/List'

// The meta data for your routes
const meta = require('./meta.json')

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home,
      meta: meta['/']
    },
    {
      path: '/list',
      name: 'List',
      component: List,
      meta: meta['/list']
    },
    // Global redirect for 404
    { path: '*', redirect: '/'}
  ],
})
