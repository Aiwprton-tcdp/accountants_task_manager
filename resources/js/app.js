import { createApp } from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Vue3Toastify from 'vue3-toastify'
import 'flowbite'

import App from './App.vue'
import router from '@utils/router.js'

import 'vue3-toastify/dist/index.css'

axios.defaults.baseURL = import.meta.env.VITE_APP_URL + '/api/'

// axios.interceptors.request.use(config => {
//   const token = localStorage.getItem('support_access')
//   config.headers.Authorization = token ? `Bearer ${token}` : ''
//   return config
// })

createApp(App)
  .use(router)
  .use(VueAxios, { ax: axios })
  .use(Vue3Toastify, {
    limit: 4,
    autoClose: 3000,
    style: {
      opacity: '1',
      userSelect: 'initial',
    },
  })
  .mount('#app')
