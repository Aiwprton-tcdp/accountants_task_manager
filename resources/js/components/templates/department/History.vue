<script>
import { inject } from 'vue'

import { FormatDateTime } from '@utils/validation.js'

export default {
  name: 'HistoryComponent',
  components: {},
  props: {
    id: Number(),
  },
  data() {
    return {
      actions: Array(),
      errored: Boolean(),
    }
  },
  setup() {
    const toast = inject('createToast')
    return { toast }
  },
  mounted() {
    this.GetActions()
  },
  methods: {
    GetActions() {
      this.ax.get(`actions?dep=${this.id}`).then(r => {
        this.actions = r.data.data.data
        this.actions.forEach(a => a.created_at = FormatDateTime(a.created_at))
      }).catch(e => {
        console.log(e)
        this.toast(e.response.data.message, 'error')
        this.errored = true
      })
    }
  },
}
</script>

<template>
  <div v-if="errored" class="flex flex-col">
    <p class="mx-auto">Произошла непредвиденная ошибка</p>

    <button @click="GetActions()"
      class="mx-auto text-sm pb-1 no-underline hover:underline border-0 focus:outline-none bg-transparent decoration-dotted underline-offset-4">
      <p>Перезагрузить</p>
    </button>
  </div>

  <div v-else-if="actions.length == 0">
    <p class="mx-auto text-center text-gray-400 w-full lg:w-2/3">
      Пока не было совершено ни одного действия
    </p>
  </div>

  <div v-else class="flex flex-col gap-1">
    <div class="text-sm">
      <template v-for="a in actions" v-bind:key="a">
        <div class="flex flex-row gap-2">
          <p class="text-gray-400">[{{ a.created_at }}]</p>
          <p>{{ a.content }}</p>
        </div>
      </template>
    </div>
  </div>
</template>