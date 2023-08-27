<script>
import { inject } from 'vue'
import {
  Table as VueTable, TableHead,
  TableBody, TableHeadCell,
  TableRow, TableCell,
  Input as VueInput,
  Button as VueButton,
  Select as VueSelect,
} from 'flowbite-vue'

import { StringVal } from '@/utils/validation.js'

export default {
  name: 'LLC_Page',
  components: {
    VueTable, TableHead,
    TableBody, TableHeadCell,
    TableRow, TableCell,
    VueInput, VueButton,
    VueSelect
  },
  data() {
    return {
      All_LLC: Array(),
      llcs: Array(),
      users: Array(),
      errored: Boolean(),
      CreatingUserCrmId: Number(),
      CreatingName: String(),
      PatchingId: Number(),
      PatchingUserCrmId: Number(),
      PatchingName: String(),
      search: String(),
    }
  },
  setup() {
    const toast = inject('createToast')
    return { toast }
  },
  mounted() {
    this.Get()
  },
  methods: {
    Get() {
      this.ax.get('llc').then(r => {
        this.All_LLC = r.data.data.data
        this.llcs = this.All_LLC
        this.GetManagers()
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      })
    },
    GetManagers() {
      this.ax.get('managers').then(r => {
        this.users = r.data.data.data.map(d => d = {
          value: d.crm_id,
          name: d.name,
        })
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
        this.errored = true
      })
    },
    Create() {
      const name = this.CreatingName.trim()
      const validate = StringVal(name)

      if (validate.status) {
        this.toast(validate.message, 'error')
        return
      } else if (this.CreatingUserCrmId < 1) {
        this.toast('Укажите менеджера', 'error')
      }

      this.ax.post('llc', {
        name: name,
        manager_id: this.CreatingUserCrmId,
      }).then(r => {
        this.toast(r.data.message, r.data.status ? 'success' : 'error')
        if (!r.data.status) return

        this.All_LLC.push(r.data.data)
        this.llcs = this.All_LLC

        this.CreatingName = ''
        this.CreatingUserCrmId = 0
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
      })
    },
    Patch(llc_id) {
      const name = this.PatchingName.trim()
      const validate = StringVal(name)

      if (validate.status) {
        this.toast(validate.message, 'error')
        return
      } else if (this.PatchingUserCrmId < 1) {
        this.toast('Укажите менеджера', 'error')
      }

      this.ax.patch(`llc/${llc_id}`, {
        name: name,
        manager_id: this.PatchingUserCrmId,
      }).then(r => {
        this.toast(r.data.message, r.data.status ? 'success' : 'error')
        if (!r.data.status) return

        const index = this.All_LLC.findIndex(({ id }) => id == llc_id)
        this.All_LLC[index] = r.data.data
        this.llcs = this.All_LLC

        this.PatchingId = 0
        this.PatchingName = ''
        this.PatchingUserCrmId = 0
      }).catch(e => {
        this.toast(e.response.data.message, 'error')
      })
    },
    Delete(llc_id) {
      this.ax.delete(`llc/${llc_id}`).then(r => {
        this.toast(r.data.message, r.data.status ? 'success' : 'warning')
        if (!r.data.status) return

        const index = this.All_LLC.findIndex(({ id }) => id == llc_id)
        this.All_LLC.splice(index, 1)
        this.llcs = this.All_LLC
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
      const text = data.replaceAll(/[^А-яA-z ]+/g, '').trim().toLowerCase()

      this.llcs = this.All_LLC.filter(g =>
        id.length > 0 && g.id.toString().includes(id) ||
        text.length > 0 && g.name.toLowerCase().includes(text)
      )
    },
    ClearSearch() {
      this.search = ''
      this.llcs = this.All_LLC
    },
    PrepareForPatch(data = null) {
      this.PatchingId = data?.id ?? 0
      this.PatchingUserCrmId = data?.manager_id ?? 0
      this.PatchingName = data?.name
    },
  },
}
</script>

<template>
  <!-- Search -->
  <div class="fixed top-1 right-1">
    <div v-if="All_LLC.length > 0" class="flex flex-row space-x-2">
      <VueInput @keyup.enter="Search()" v-model="search" placeholder="Поиск" label="" class="w-64">
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
      <VueButton :disabled="search.length == 0" @click="Search()" color="default">Искать</VueButton>
    </div>
  </div>

  <VueTable
    class="max-h-[calc(100vh-54px)] overflow-y-auto overscroll-none scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
    <TableHead>
      <TableHeadCell>Id</TableHeadCell>
      <TableHeadCell>Название</TableHeadCell>
      <TableHeadCell>Ответственный</TableHeadCell>
      <TableHeadCell><span class="sr-only">Edit</span></TableHeadCell>
    </TableHead>

    <TableBody>
      <TableRow>
        <TableCell></TableCell>
        <TableCell>
          <VueInput v-model="CreatingName" placeholder="Введите название организации" />
        </TableCell>
        <TableCell>
          <VueSelect v-model="CreatingUserCrmId" :options="users" />
        </TableCell>
        <TableCell>
          <div class="space-x-3">
            <VueButton :disabled="CreatingName.length < 3 || CreatingUserCrmId == 0" @click="Create()" color="green">
              Добавить
            </VueButton>
            <VueButton v-if="CreatingName.length > 0 && CreatingUserCrmId > 0"
              @click="CreatingName = ''; CreatingUserCrmId = 0" color="light">Сброс</VueButton>
          </div>
        </TableCell>
      </TableRow>

      <TableRow v-for="l in llcs" v-bind:key="l">
        <TableCell>{{ l.id }}</TableCell>
        <TableCell>
          <p v-if="PatchingId != l.id">{{ l.name }}</p>
          <VueInput v-else v-model="PatchingName" placeholder="Введите новое название" />
        </TableCell>
        <TableCell>
          <p v-if="PatchingId != l.id">{{ l.user_name }}</p>
          <VueSelect v-else v-model="PatchingUserCrmId" :value="l.manager_id" :options="users" />
        </TableCell>
        <TableCell>
          <div v-if="PatchingId == l.id" class="space-x-3">
            <VueButton @click="Patch(l.id)" color="green">Сохранить</VueButton>
            <VueButton @click="PrepareForPatch()" color="light">Отменить</VueButton>
          </div>
          <div v-else class="space-x-3">
            <VueButton @click="PrepareForPatch(l)" color="light">Редактировать</VueButton>
            <!-- <VueButton @click="Delete(l.id)" color="red">Удалить</VueButton> -->
          </div>
        </TableCell>
      </TableRow>
    </TableBody>
  </VueTable>

  <div v-if="errored || llcs.length == 0" class="flex flex-col gap-3 mt-3">
    <p v-if="errored" class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
      Произошла непредвиденная ошибка
    </p>
    <p v-else-if="llcs.length == 0" class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
      Нет данных
    </p>
  </div>
</template>