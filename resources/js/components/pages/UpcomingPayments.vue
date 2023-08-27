<script>
import { inject } from 'vue'

export default {
  name: 'UpcomingPaymentsPage',
  components: {},
  data() {
    return {
      contracts: Array(),
      errored: Boolean(),
      waiting: Boolean(),
    }
  },
  setup() {
    const UserData = inject('UserData')
    const toast = inject('createToast')

    return { UserData, toast }
  },
  mounted() {
    this.GetContracts()
  },
  methods: {
    GetContracts() {
      if (this.waiting) return
      this.waiting = true

      let user = this.UserData.is_manager ? `user_id=${this.UserData.crm_id}&` : ''
      this.ax.get(`contracts?${user}limit=10`).then(r => {
        this.contracts = r.data.data.data

        this.contracts.forEach(c => {
          const d = new Date(c.next_payment_date)

          c.next_payment_date = d.toLocaleDateString('ru-RU', {
            weekday: 'short',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          })
          c.need_to_pay = d.setDate(d.getDate() - 3) <= new Date().getTime()
        })
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      }).finally(() => this.waiting = false)
    },
    Create(contract_id) {
      if (!window.confirm("Нажав 'ОК', Вы подтверждаете, что оплата договора была завершена")) {
        return
      }

      this.ax.patch(`contracts/${contract_id}`, {
        payed: true,
      }).then(r => {
        if (!r.data.status) {
          this.toast(r.data.message, 'error')
          return
        }
        this.toast(r.data.message, 'success')
        this.GetContracts()
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
      })
    },
  },
}
</script>

<template>
  <div
    class="h-[calc(100vh-55px)] col-span-2 flex flex-col gap-3 pt-3 overflow-y-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
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
      <button @click="GetContracts()"
        class="mx-auto text-sm pb-1 no-underline hover:underline border-0 focus:outline-none bg-transparent decoration-dotted underline-offset-4">
        <p>Перезагрузить</p>
      </button>
    </div>
    <p v-else-if="contracts.length == 0" class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
      Нет запланированных платежей
    </p>

    <div v-else class="flex flex-col gap-2 items-center">
      <template v-for="c in contracts" v-bind:key="c">
        <div
          class="block min-w-md w-[90vw] px-6 py-4 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
          <div class="flex flex-row gap-2 justify-between">
            <p class="w-[25%] truncate" :title="c.contract_type_name">{{ c.contract_type_name }}</p>
            <p class="w-[30%] text-gray-400 truncate">ООО "{{ c.llc_name }}"</p>
            <p class="w-[340px]">
              Следующая оплата:
              <span :class="{ 'text-red-500': c.need_to_pay }">
                {{ c.next_payment_date }}
              </span>
            </p>

            <div @click="Create(c.id)" :class="{ 'text-red-500': c.need_to_pay }"
              class="cursor-pointer hover:text-green-500 focus:text-green-500" title="Оплачено">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
              </svg>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>