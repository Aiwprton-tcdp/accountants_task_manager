<script>
import { inject } from 'vue'
import { Tabs, Tab } from 'flowbite-vue'

export default {
  name: 'HeaderComponent',
  components: { Tabs, Tab },
  setup() {
    const ActiveTab = inject('ActiveTab')
    const UserData = inject('UserData')

    return { ActiveTab, UserData }
  },
  methods: {
    ChangeRoute() {
      this.$router.push({ name: this.ActiveTab })
    }
  },
}
</script>

<template>
  <Tabs variant="underline" v-model="ActiveTab" @click:pane="ChangeRoute()">
    <template v-if="UserData.is_admin
      || UserData.is_manager
      || UserData.crm_id == 4942">
      <Tab name="upcoming_payments" title="Ближайшие платежи" />
    </template>

    <Tab name="departments" title="Отделы" />

    <template v-if="UserData.is_admin
      || UserData.is_manager
      || UserData.crm_id == 4942">
      <Tab name="llc" title="ООО" />
      <Tab name="users" title="Пользователи" />
    </template>
  </Tabs>
</template>
