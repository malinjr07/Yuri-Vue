<template>
  <div class="task-filter-bar">
    <select
      v-model="selectedAssignedUser"
      @change="selectedAssignedUserChanged()"
    >
      <option
        v-for="user in selectOptionsAssignedUsers.items"
        :value="user.ID"
        :key="user.ID"
      >
        {{ user.nickname }}
      </option>
    </select>
  </div>
</template>
<script>
// import
// import axios from "axios";

// import {tempoUtils} from '../tempoUtils.js'

export default {
  components: {},
  props: {},
  emits: ["selectedAssignedUserChanged"], //{
  //selectedAssignedUserChanged: ({ testing }) => {

  //alert('validating custom event: ' + testing);
  //can do validation on the event here.
  //    return true;
  //}

  //},
  data() {
    return {
      selectedAssignedUser: 4,
      selectOptionsAssignedUsers: { items: [] },
    };
  },
  mounted() {
    this.getUserList();
    //this.$_tempoUtils.testGlobalFunc('filter mounted2');
  },
  methods: {
    selectedAssignedUserChanged() {
      this.$emit("selectedAssignedUserChanged", this.selectedAssignedUser);
    },

    getUserList() {
      let url =
        window.tempoApiPath +
        "users/read.php?assignedUserId=" +
        this.selectedAssignedUser;

      this.$_axios
        .get(url, {
          auth: {
            username: window.tempoApiUsername,
            password: window.tempoApiPassword,
          },
        })
        .then((response) => {
          this.selectOptionsAssignedUsers = response.data;
        });

      //return [
      //   { userId: 2, displayName: 'Chris' },
      //    { userId: 4, displayName: 'Scott2' },
      //    { userId: 1, displayName: 'Nathanael' },
      //];
    },
  },
};
</script>
<style scoped>
</style>