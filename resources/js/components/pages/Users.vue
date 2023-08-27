<script>
import { inject } from 'vue'
import {
  Table as VueTable, TableHead,
  TableBody, TableHeadCell,
  TableRow, TableCell,
  Input as VueInput,
  Button as VueButton,
  Toggle
} from 'flowbite-vue'

export default {
  name: 'UsersPage',
  components: {
    VueTable, TableHead,
    TableBody, TableHeadCell,
    TableRow, TableCell,
    VueInput, VueButton,
    Toggle
  },
  data() {
    return {
      AllUsers: Array(),
      users: Array(),
      errored: Boolean(),
      waiting: Boolean(),
      search: String(),
    }
  },
  setup() {
    const UserData = inject('UserData')
    const toast = inject('createToast')

    return { UserData, toast }
  },
  mounted() {
    this.GetUsers()
  },
  methods: {
    GetUsers() {
      if (this.waiting) return
      this.waiting = true

      this.ax.get('bx/users').then(r => {
        this.AllUsers = r.data.data.data
        this.users = this.AllUsers
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      }).finally(() => this.waiting = false)
    },
    Change(data) {
      if (data.is_manager) {
        this.Create(data)
      } else {
        this.Delete(data)
      }

      if (this.UserData.crm_id == data.crm_id) {
        this.UserData.is_manager = data.is_manager
      }
    },
    Create(data) {
      this.ax.post('managers', {
        crm_id: data.crm_id,
        name: data.name,
      }).then(r => {
        this.toast(r.data.message, r.data.status ? 'success' : 'error')
        data.is_manager = r.data.status
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
      })
    },
    Delete(data) {
      console.log(data)
      this.ax.delete(`managers/${data.crm_id}`).then(r => {
        this.toast(r.data.message, r.data.status ? 'success' : 'error')
        data.is_manager = !r.data.status
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
      })
    },
    Search() {
      let data = this.search.trim()
      if (data.length == 0) {
        this.ClearSearch()
        return
      }

      const id = data.replaceAll(/[^0-9]+/g, '').trim()
      const text = data.replaceAll(/[^А-я ]+/g, '').trim().toLowerCase()
      const expression = u =>
        id.length > 0 && u.crm_id.toString().includes(id)
        || text.length > 0
        && (u?.name.toLowerCase().includes(text))

      this.users = this.AllUsers.filter(expression)
      // this.users = (this.only_with_roles ? this.AllManagers : this.AllUsers).filter(expression)
    },
    ClearSearch() {
      this.search = ''
      this.users = this.AllUsers.length == 0 ? this.GetUsers() : this.AllUsers
    },
  }
}
</script>

<template>
  <div v-if="AllUsers.length > 0" class="fixed top-1 right-1 space-y-4">
    <div class="flex flex-wrap space-x-4">
      <div class="flex flex-row space-x-2">
        <VueInput @keyup.enter="Search()" v-model="search" placeholder="Поиск" label="" class="w-48">
          <template #prefix v-if="search.length == 0">
            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
              viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </template>
          <template #suffix v-else>
            <svg @click="ClearSearch()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke-width="1.5" stroke="currentColor" class="text-black-800 w-5 h-5 cursor-pointer">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.211-.211.498-.33.796-.33H19.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25h-9.284c-.298 0-.585-.119-.796-.33z" />
            </svg>
          </template>
        </VueInput>
        <VueButton :disabled="search.length == 0" @click="Search()" color="default">
          Искать
        </VueButton>
      </div>
    </div>
  </div>

  <template v-if="waiting">
    <div role="status" class="space-y-3 max-w-sm animate-pulse">
      <template v-for="key in 10" v-bind:key="key">
        <div class="flex items-center w-full space-x-2">
          <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-32"></div>
          <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24"></div>
          <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-full"></div>
        </div>
      </template>
    </div>
  </template>
  <div v-else-if="errored" class="flex flex-col">
    <p class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
      Произошла непредвиденная ошибка
    </p>
    <button @click="GetUsers()"
      class="mx-auto text-sm pb-1 no-underline hover:underline border-0 focus:outline-none bg-transparent decoration-dotted underline-offset-4">
      <p>Перезагрузить</p>
    </button>
  </div>
  <p v-else-if="users.length == 0" class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
    Не удалось найти пользователей
  </p>

  <VueTable v-else
    class="max-h-[calc(100vh-54px)] overflow-y-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
    <TableHead>
      <TableHeadCell>Crm_id</TableHeadCell>
      <TableHeadCell>ФИО</TableHeadCell>
      <TableHeadCell>Должность</TableHeadCell>
      <TableHeadCell>Менеджер</TableHeadCell>
    </TableHead>

    <TableBody>
      <TableRow v-for="u in users" v-bind:key="u">
        <TableCell>{{ u.crm_id }}</TableCell>
        <TableCell>{{ u?.name }}</TableCell>
        <TableCell>{{ u?.post }}</TableCell>
        <TableCell>
          <Toggle @change="Change(u)" v-model="u.is_manager" color="green" />
        </TableCell>
      </TableRow>
    </TableBody>
  </VueTable>
</template>
