<script>
import { inject } from 'vue'
import {
  Table as VueTable, TableHead,
  TableBody, TableHeadCell,
  TableRow, TableCell,
  Input as VueInput,
  Button as VueButton,
  Select as VueSelect,
  Tabs, Tab
} from 'flowbite-vue'

import HistoryComponent from '@temps/department/History.vue'
import NewContractComponent from '@temps/department/NewContract.vue'

export default {
  name: 'DepartmentPage',
  components: {
    VueTable, TableHead,
    TableBody, TableHeadCell,
    TableRow, TableCell,
    VueInput, VueButton,
    VueSelect, Tabs, Tab,
    NewContractComponent,
    HistoryComponent
  },
  props: {
    id: Number(),
  },
  data() {
    return {
      dep: Object(),
      contracts: Array(),
      errored: Boolean(),
      ActiveTab: String('history'),
    }
  },
  setup() {
    const toast = inject('createToast')
    return { toast }
  },
  mounted() {
    this.GetDepartmentData()
    // this.folderInit()
  },
  methods: {
    GetDepartmentData() {
      this.ax.get(`departments/${this.id}`).then(r => {
        this.dep = r.data.data
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      }).finally(this.GetContracts)
    },
    GetContracts() {
      this.ax.get(`contracts?dep=${this.id}`).then(r => {
        this.contracts = r.data.data.data
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      })
    },
    DeleteContract(contract_id) {
      this.ax.delete(`contracts/${contract_id}`).then(r => {
        this.toast(r.data.message, r.data.status ? 'success' : 'error')
        if (r.data.status) {
          const index = this.contracts.findIndex(({ id }) => id == contract_id)
          if (index > -1) this.contracts.splice(index, 1)
        }
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
      })
    },
    // folderInit() {
    //   this.ax.get('bx/parent_folder_id').then(r => {
    //     console.log(r)
    //   }).catch(e => {
    //     this.toast(e.response.data.message, 'error')
    //     this.errored = true
    //   })
    // }
  },
}
</script>

<template>
  <div class="grid grid-cols-3 h-[calc(100vh-55px)]">
    <div class="col-span-2 flex flex-col gap-3">
      <div class="flex flex-col gap-3">
        <p class="text-xl">Отдел "{{ this.dep.name }}"</p>
        <p class="text-xl">ООО "{{ this.dep.llc_name }}"</p>
      </div>

      <div
        class="max-h-[60vh] flex flex-col gap-2 overflow-y-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
        <template v-for="c in contracts" v-bind:key="c">
          <div class="flex flex-row gap-2 items-center">
            <p>{{ c.contract_type_name }}:</p>
            <p>{{ c.name }}</p>
            <a :href="c.link" title="Скачать">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
              </svg>
            </a>
            <div @click="DeleteContract(c.id)" class="cursor-pointer text-red-500" title="Удалить">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
              </svg>
            </div>
          </div>
        </template>
      </div>

      <div v-if="errored || contracts.length == 0" class="flex flex-col gap-3 mt-3">
        <p v-if="errored" class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
          Произошла непредвиденная ошибка
        </p>
        <p v-else-if="contracts.length == 0" class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
          Нет данных
        </p>
      </div>
    </div>

    <div>
      <div class="tabs-nowrap truncate">
        <Tabs variant="underline" v-model="ActiveTab">
          <Tab name="history" title="История" />
          <Tab name="new_contract" title="Добавление договора" />
        </Tabs>
      </div>

      <div
        class="max-h-[calc(100vh-55px-55px)] p-2 overflow-y-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
        <NewContractComponent :id="id" :class="{ 'hidden': ActiveTab != 'new_contract' }" />
        <HistoryComponent v-if="ActiveTab == 'history'" :id="id" />
      </div>
    </div>
  </div>
</template>