<template>
  <Menubar :model="items">
    <template #start>
      <div>
        <router-link to="/" class="logo">
          Tempo
        </router-link>
      </div>
    </template>
    <template #end>
        <div>
          <input class="p-inputtext p-component" placeholder="Search" type="text">
        </div>
    </template>
  </Menubar>

</template>

<script>
import Menubar from 'primevue/menubar';

export default {
  name: 'App',
  components: {
    Menubar
  },
  data() {
      return {
          items: [
              {
                  label:'Tasks',
                  icon:'pi pi-fw pi-file',
                  to: '/tasks',
                  
              },
              {
                  label:'Queues',
                  icon:'pi pi-fw pi-pencil',
                  to: '/queues',
              },
              {
                  label:'Projects',
                  icon:'pi pi-fw pi-user',
                  to: '/projects',
              },
              {
                  label:'Leads',
                  icon:'pi pi-fw pi-calendar',
                  to: '/leads',
              }

            ]
      }
  }
}

</script>

<style>
  .logo {
    font-size: 30px;
    text-decoration: none;
  }
</style>