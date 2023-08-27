<script>
import { inject } from 'vue'
import {
  Input as VueInput,
  Button as VueButton,
  Select as VueSelect,
} from 'flowbite-vue'
import DatePicker from 'vue-datepicker-next'

import 'vue-datepicker-next/index.css'

export default {
  name: 'NewContractComponent',
  components: {
    VueInput, VueButton,
    VueSelect, DatePicker
  },
  props: {
    id: Number(),
  },
  data() {
    return {
      contracts: Array(),
      contract_types: Array(),
      errored: Boolean(),
      CreatingFile: Object(),
      CreatingContractTypeId: Number(),
      CreatingPaymentPeriodCount: Number(1),
      CreatingPaymentPeriodType: String('m'),
      CreatingPaymentDate: Date(),
      dragging: Boolean(),
    }
  },
  setup() {
    const toast = inject('createToast')
    return { toast }
  },
  mounted() {
    this.GetContractTypes()
  },
  methods: {
    GetContractTypes() {
      this.ax.get('contract_types').then(r => {
        this.contract_types = r.data.data.data.map(d => d = {
          name: d.value,
          value: d.id,
        })
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      })
    },
    Create() {
      if (this.CreatingPaymentPeriodCount < 1) {
        this.toast('Укажите количество периодов', 'warning')
      }

      const date = new Date(this.CreatingPaymentDate).toLocaleDateString('az-Cyrl-AZ', {
        month: '2-digit',
        year: 'numeric',
        day: '2-digit',
      })

      let form = new FormData()
      form.append('file', this.CreatingFile)
      form.append('department_id', this.id)
      form.append('contract_type_id', this.CreatingContractTypeId)
      form.append('payment_period_count', this.CreatingPaymentPeriodCount)
      form.append('payment_period_type', this.CreatingPaymentPeriodType)
      form.append('next_payment_date', date)

      this.ax.post('contracts', form).then(r => {
        this.toast(r.data.message, r.data.status ? 'success' : 'error')
        if (!r.data.status) return

        this.$parent.$data.contracts.push(r.data.data)

        this.ClearDataForCreating()
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
      })
    },
    AddContractFile(event) {
      if (event.target.files.length == 0) {
        console.log('There are no files for uploading')
        return
      }

      const types = [
        'application/msword',
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        // 'text/plain',
      ]

      const file = event.target.files[0]
      if (!types.includes(file['type'])) {
        this.toast('Вы можете отправить только документы: .doc, .docx, .pdf', 'warning')
        return
      }
      this.$refs.contract_file.value = null
      this.CreatingFile = file
      this.dragging = false
    },
    DragFiles(e) {
      var files = e.target.files || e.dataTransfer.files

      if (!files.length) {
        this.dragging = false
        return
      }

      this.AddContractFile(e)
    },
    ClearDataForCreating() {
      this.CreatingFile = null
      this.CreatingContractTypeId = 0
      this.CreatingPaymentPeriodCount = 1
      this.CreatingPaymentPeriodType = 'm'
      this.CreatingPaymentDate = null
    }
  },
}
</script>

<template>
  <div class="flex flex-col gap-1">
    <label for="contract_type">Тип договора</label>
    <VueSelect id="contract_type" v-model="CreatingContractTypeId" :options="contract_types" />

    <div @dragenter="dragging = true" :class="{ 'dropZone-over': dragging }" class="dropZone my-3">
      <div @drag="DragFiles" class="flex flex-col h-full mx-2 my-3 items-center justify-center">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
          </svg>
        </div>
        <span class="text-center">
          {{ CreatingFile?.name ?? 'Перетащите файл или нажмите, чтобы загрузить' }}
        </span>
      </div>
      <input type="file" accept="application/*" ref="contract_file" @change="DragFiles">
    </div>

    <label for="period_days">Периодичность платежей</label>
    <div class="flex">
      <select id="states" v-model="CreatingPaymentPeriodType"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg border-r-gray-100 dark:border-r-gray-700 border-r-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="d">Дней</option>
        <option value="w">Недель</option>
        <option value="m" selected>Месяцев</option>
        <option value="q">Кварталов</option>
        <option value="y">Лет</option>
      </select>
      <input id="period_days" v-model="CreatingPaymentPeriodCount" type="number"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg border-l-gray-100 dark:border-l-gray-700 border-l-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
    </div>

    <label for="payment_date">Дата следующего платежа</label>
    <DatePicker id="payment_date" v-model:value="CreatingPaymentDate" type="date" editable="false" />

    <div class="space-x-3">
      <VueButton :disabled="CreatingContractTypeId == 0
        || CreatingPaymentPeriodCount == 0
        || CreatingPaymentDate == null
        || CreatingFile?.name == null" @click="Create()" color="green">
        Добавить
      </VueButton>
      <VueButton :disabled="CreatingContractTypeId == 0
        && CreatingPaymentPeriodCount == 0
        && CreatingPaymentDate == null
        && CreatingFile?.name == null" @click="ClearDataForCreating()" color="light">
        Сброс
      </VueButton>
    </div>
  </div>
</template>

<style>
.dropZone {
  width: 100%;
  height: 100%;
  position: relative;
  border: 2px dashed #aaa;
}

.dropZone input {
  position: absolute;
  cursor: pointer;
  top: 0px;
  right: 0;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
}

.dropZone-over {
  background: #eee;
  opacity: 0.8;
}
</style>