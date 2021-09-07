<template>
  <div id="app">
    <nav class="navbar navbar-expand navbar-dark bg-dark">
      <a href="/" class="navbar-brand">Tempo</a>
      <div class="navbar-nav mr-auto">
        <li class="nav-item">
          <router-link to="/" class="nav-link">
            <font-awesome-icon icon="home" /> Home
          </router-link>
        </li>
        <li v-if="currentUser" class="nav-item">
          <router-link to="/queues" class="nav-link">
            <font-awesome-icon icon="home" /> Queue
          </router-link>
        </li>
        <li v-if="currentUser" class="nav-item">
          <router-link to="/tasks" class="nav-link">
            <font-awesome-icon icon="home" /> Tasks
          </router-link>
        </li>
        <li v-if="currentUser" class="nav-item">
          <router-link to="/projects" class="nav-link">
            <font-awesome-icon icon="home" /> Projects
          </router-link>
        </li>
        <li v-if="currentUser" class="nav-item">
          <router-link to="/leads" class="nav-link">
            <font-awesome-icon icon="home" /> Leads
          </router-link>
        </li>
      </div>

      <div v-if="!currentUser" class="navbar-nav ml-auto">
        <li class="nav-item">
          <router-link to="/login" class="nav-link">
            <font-awesome-icon icon="sign-in-alt" /> Login
          </router-link>
        </li>
      </div>

      <div v-if="currentUser" class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" @click.prevent="logOut">
            <font-awesome-icon icon="sign-out-alt" /> LogOut
          </a>
        </li>
      </div>
    </nav>

    <div class="container">
      <router-view />
    </div>
  </div>
</template>

<script>
import EventBus from "./common/EventBus";

export default {
  name: "App",
  components: {},
  computed: {
    currentUser() {
      return this.$store.state.auth.user;
    },
  },
  methods: {
    logOut() {
      this.$store.dispatch("auth/logout");
      this.$router.push("/login");
    },
  },
  mounted() {
    EventBus.on("logout", () => {
      this.logOut();
    });
  },
  beforeUnmount() {
    EventBus.remove("logout");
  },
};
</script>

<style>
.container {
  max-width: 1280px !important;
}
</style>