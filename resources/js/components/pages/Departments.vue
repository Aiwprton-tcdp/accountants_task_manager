<script>
import { inject } from 'vue'
import {
  Input as VueInput,
  Button as VueButton,
} from 'flowbite-vue'

import { StringVal } from '@utils/validation.js'

export default {
  name: 'DepartmentsPage',
  components: {
    VueInput, VueButton,
  },
  props: {
    id: Number()
  },
  data() {
    return {
      AllDepartments: Array(),
      tickets: Array(),
      CurrentTicket: Object(),
      managers: Array(),
      TicketsHistory: new Map(),
      errored: Boolean(),
      waiting: Boolean(),
      searching: Boolean(),
      ticket401: Boolean(),
      ShowNewDepartment: Boolean(),
      NewDepartmentLLC: String(),
      CreatingDepartment: String(),
      search: String(),
      page: Number(),
      TicketsCount: Number(),
      limit: Number(15),
    }
  },
  setup() {
    const UserData = inject('UserData')
    const toast = inject('createToast')
    // const emitter = inject('emitter')
    const ActiveTab = inject('ActiveTab')

    return {
      UserData, toast,
      // emitter,
      ActiveTab
    }
  },
  mounted() {
    this.Get()
  },
  methods: {
    Get() {
      if (this.waiting) return
      this.waiting = true

      this.ax.get('departments').then(r => {
        this.errored = !r.data.status
        this.AllDepartments = r.data.data.data
        this.GroupedDepartments = this.GroupByLLC(this.AllDepartments)
        this.errored = false
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      }).finally(() => this.waiting = false)
    },
    Create(id) {
      const name = this.CreatingDepartment.trim()
      const validate = StringVal(name)

      if (validate.status) {
        this.toast(validate.message, 'error')
        return
      }

      this.ax.post('departments', {
        name: name,
        l_l_c_s_id: id,
      }).then(r => {
        if (!r.data.status) {
          this.toast(r.data.message, 'error')
          return
        }
        this.toast(r.data.message, 'success')

        this.ShowNewDepartment = false
        this.GroupedDepartments[this.NewDepartmentLLC].push(r.data.data)
        this.CreatingDepartment = ''
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        data.is_manager = false
      })
    },
    GroupByLLC(data) {
      return data.reduce(function (rv, x) {
        (rv[x['llc_name']] = rv[x['llc_name']] || []).push(x)
        return rv
      }, {})
    },
    GoTo(d) {
      this.$router.push({ name: 'department', params: { id: d.id } })
    },
  }
}
</script>

<template>
  <!-- <div class="flex flex-col divide-x max-h-[calc(100vh-55px)]"> -->
  <!-- <div class="h-[calc(100vh-55px)] flex flex-col items-center"> -->
  <template v-if="waiting">
    <div role="status" class="space-y-2.5 max-w-sm animate-pulse">
      <div class="flex items-center w-full space-x-2">
        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-44"></div>
        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-44"></div>
        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-44"></div>
        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-44"></div>
        <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-44"></div>
      </div>
    </div>
    <!-- <div
      class="flex flex-col h-[calc(100vh-55px)] divide-y overflow-y-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
      <div v-for="key in 10" v-bind:key="key" class="flex flex-row items-center w-full gap-2 p-1">
        <svg class="w-10 h-10 text-gray-200 dark:text-gray-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="currentColor" viewBox="0 0 20 20">
          <path
            d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
        </svg>
        <div class="max-w-[80%] flex flex-col">
          <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-32 mb-2"></div>
          <div class="w-48 h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
        </div>
      </div>
    </div> -->
  </template>

  <div v-else-if="errored" class="flex flex-col">
    <p class="mx-auto">Произошла непредвиденная ошибка</p>

    <button @click="Get()"
      class="mx-auto text-sm pb-1 no-underline hover:underline border-0 focus:outline-none bg-transparent decoration-dotted underline-offset-4">
      <p>Перезагрузить</p>
    </button>
  </div>

  <div v-else
    class="flex flex-row h-[calc(100vh-55px-10px)] px-10 gap-x-10 overflow-x-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
    <template v-for="(gd, name) in GroupedDepartments" v-bind:key="gd">
      <div class="flex flex-col gap-4 min-w-fit w-[16vw]">
        <div class="flex flex-row gap-1 items-center justify-center">
          <p class="text-xl">{{ name }}</p>

          <div class="relative">
            <span @click="ShowNewDepartment = !ShowNewDepartment; NewDepartmentLLC = name"
              class="cursor-pointer font-bold text-blue-400" title="Добавить отдел">
              &#10010;
            </span>
            <div :class="ShowNewDepartment && NewDepartmentLLC == name ? 'visible opacity-100' : 'invisible opacity-0'"
              class="absolute left-[-10vw] right-[-10vw] top-30 flex flex-col gap-2 p-3 inline-block text-sm font-light text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm w-fit dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
              <p>Добавление подразделения в организацию {{ name }}:</p>
              <VueInput v-model="CreatingDepartment" placeholder="Введите название" />
              <VueButton @click="Create(gd[0].l_l_c_s_id)" color="default">
                Добавить
              </VueButton>
            </div>
          </div>
        </div>

        <div @click="ShowNewDepartment = false"
          class="flex flex-col gap-1 h-[calc(100%-60px)] divide-y overflow-y-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
          <template v-for="d in gd" v-bind:key="d">
            <p @click="GoTo(d)" class="cursor-pointer">{{ d.name }}</p>
          </template>
        </div>
      </div>
    </template>
  </div>
  <!-- </div> -->
  <!-- </div> -->
</template>