<template>
  <div>
    <p>Here are your tasks :</p>

    <TaskFilterBar
      @selectedAssignedUserChanged="filterUserChanged"
    ></TaskFilterBar>

    <transition name="tempo-modal-transition">
      <TaskCardModal
        v-if="isModalVisible"
        @close="closeModal"
        v-bind:task="modalTask"
        @taskTitleChanged="taskTitleChanged"
        @taskDescriptionChanged="taskDescriptionChanged"
        @taskNewNoteAdded="taskNewNoteAdded"
      ></TaskCardModal>
    </transition>

    <div v-show="isLoading">Loading...</div>

    <div v-show="!isLoading">
      <div class="tasks-cols-container">
        <div
          class="tasks-col"
          :class="{ 'tasks-col-collapsed': colCalendarCollapsed }"
          @click="if (colCalendarCollapsed) colCalendarCollapsed = false;"
        >
          <h2
            class="tasks-col-title"
            @click.stop="colCalendarCollapsed = !colCalendarCollapsed"
          >
            Your Calendar
          </h2>

          <template v-if="!colCalendarCollapsed">
            <iframe
              src="https://calendar.google.com/calendar/embed?height=600&wkst=2&bgcolor=%23ffffff&ctz=Australia%2FBrisbane&src=c2NvdHRAZXhjaXRlbWVkaWEuY29tLmF1&src=c2NvdHRtYXluYXJkQGdtYWlsLmNvbQ&color=%233F51B5&color=%23039BE5&title=Scott's%20Day&showTz=0&mode=AGENDA&showTitle=0&showDate=0&showPrint=0&showTabs=1&showCalendars=1"
              style="border-width:0:min-width: 300px;width:100%"
              height="600"
              frameborder="0"
              scrolling="no"
            ></iframe>
          </template>
        </div>

        <div
          class="tasks-col"
          :class="{ 'tasks-col-collapsed': colTasksTodayCollapsed }"
          @click="if (colTasksTodayCollapsed) colTasksTodayCollapsed = false;"
        >
          <h2
            class="tasks-col-title"
            @click.stop="colTasksTodayCollapsed = !colTasksTodayCollapsed"
          >
            Today's Tasks ({{ tasksToday.length }})
          </h2>

          <template v-if="!colTasksTodayCollapsed">
            <draggable
              :list="tasksToday"
              item-key="ID"
              v-bind="dragOptions"
              :component-data="{
                type: 'transition-group',
                name: !drag ? 'flip-list' : null,
              }"
              @start="drag = true"
              @end="drag = false"
              @change="moveToday"
            >
              <template #item="{ element }">
                <TaskItem :task="element" @openTask="openTaskCard" />
              </template>
            </draggable>
          </template>
        </div>

        <div
          class="tasks-col"
          :class="{ 'tasks-col-collapsed': colTasksTomorrowCollapsed }"
          @click="
            if (colTasksTomorrowCollapsed) colTasksTomorrowCollapsed = false;
          "
        >
          <h2
            class="tasks-col-title"
            @click.stop="colTasksTomorrowCollapsed = !colTasksTomorrowCollapsed"
          >
            Tomorrow's Tasks ({{ tasksTomorrow.length }})
          </h2>

          <template v-if="!colTasksTomorrowCollapsed">
            <draggable
              :list="tasksTomorrow"
              item-key="ID"
              v-bind="dragOptions"
              :component-data="{
                type: 'transition-group',
                name: !drag ? 'flip-list' : null,
              }"
              @start="drag = true"
              @end="drag = false"
              @change="moveTomorrow"
            >
              <template #item="{ element }">
                <TaskItem :task="element" @openTask="openTaskCard" />
              </template>
            </draggable>
          </template>
        </div>

        <div
          class="tasks-col"
          :class="{ 'tasks-col-collapsed': colTasksOtherCollapsed }"
          @click="if (colTasksOtherCollapsed) colTasksOtherCollapsed = false;"
        >
          <h2
            class="tasks-col-title"
            @click.stop="colTasksOtherCollapsed = !colTasksOtherCollapsed"
          >
            Coming Up ({{ tasksOther.length }})
          </h2>

          <template v-if="!colTasksOtherCollapsed">
            <draggable
              :list="tasksOther"
              item-key="ID"
              v-bind="dragOptions"
              :component-data="{
                type: 'transition-group',
                name: !drag ? 'flip-list' : null,
              }"
              @start="drag = true"
              @end="drag = false"
              @change="moveComing"
            >
              <template #item="{ element }">
                <div>
                  <TaskItem :task="element" @openTask="openTaskCard" />
                </div>
              </template>
            </draggable>
          </template>
        </div>

        <div
          class="tasks-col"
          :class="{ 'tasks-col-collapsed': colTasksNoDueDateCollapsed }"
          @click="
            if (colTasksNoDueDateCollapsed) colTasksNoDueDateCollapsed = false;
          "
        >
          <h2
            class="tasks-col-title"
            @click.stop="
              colTasksNoDueDateCollapsed = !colTasksNoDueDateCollapsed
            "
          >
            No Due Date ({{ tasksNoDueDate.length }})
          </h2>

          <template v-if="!colTasksNoDueDateCollapsed">
            <draggable
              :list="tasksNoDueDate"
              item-key="ID"
              v-bind="dragOptions"
              :component-data="{
                type: 'transition-group',
                name: !drag ? 'flip-list' : null,
              }"
              @start="drag = true"
              @end="drag = false"
              @change="moveNoDue"
            >
              <template #item="{ element }">
                <div>
                  <TaskItem :task="element" @openTask="openTaskCard" />
                </div>
              </template>
            </draggable>

            <Pagination
              :itemsCount="tasksNoDueDate.length"
              :pageSize="tasksNoDueDatePages"
              :onPageChange="handleNoDuePageChange"
            />
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import { paginate } from "../utils/functions";

// import axios from 'axios'
// import {tempoUtils} from '../tempoUtils.js'
// import TempoModal from '../components/TempoModal.vue'

import Pagination from "../components/Pagination.vue";
import TaskItem from "../components/TaskItem.vue";
import TaskCardModal from "../components/TaskCardModal.vue";
import TaskFilterBar from "../components/TaskFilterBar.vue";
import TokenService from "../services/token.service";

export default {
  components: {
    Pagination,
    TaskItem,
    TaskCardModal,
    TaskFilterBar,
    draggable,
  },

  computed: {
    dragOptions() {
      return {
        animation: 200,
        disabled: false,
        ghostClass: "ghost",
        group: "tempo-list-group",
      };
    },
  },

  data() {
    return {
      tasksNoDueDatePages: 5,
      tasksTodayPages: 5,
      tasksTomorrowPages: 5,
      tasksOtherPages: 5,
      debugInfo: null,
      tasks: [],
      tasksNoDueDate: [],
      tasksToday: [],
      tasksTomorrow: [],
      tasksOther: [],
      newTaskText: "",
      isLoading: true,
      isModalVisible: false,
      modalTitle: "",
      modalTask: "",
      modalFooter: "",
      selectedAssignedUserId: 4,
      drag: false,
      colTasksTodayCollapsed: false,
      colTasksTomorrowCollapsed: false,
      colTasksOtherCollapsed: false,
      colTasksNoDueDateCollapsed: false,
      colCalendarCollapsed: true,
    };
  },

  created() {
    if (!TokenService.getUser()) this.$router.push({ name: "login" });
  },

  mounted() {
    this.refreshTasks();
  },

  methods: {
    handleNoDuePageChange(e) {
      console.log(e);
    },

    handleTodayPageChange(e) {
      console.log(e);
    },

    handleTomorrowPageChange(e) {
      console.log(e);
    },

    handleOtherPageChange(e) {
      console.log(e);
    },

    moveToday({ added }) {
      const obj = JSON.parse(JSON.stringify(added.element));
      console.log("today -->", added, obj);

      const today = this.$_moment().format("YYYY-MM-DD");
      const url = window.tempoApiPath + "tasks.php";

      const data = new FormData();

      data.append("ID", obj.ID);
      data.append("due_date", today);

      this.$_axios
        .post(url, { params: data, operation: "updateTask" })
        .then((res) => {
          // this.refreshTasks();
          this.segmentTasks();
          console.log(res);
        })
        .catch((err) => console.log(err));
    },

    moveTomorrow({ added }) {
      const obj = JSON.parse(JSON.stringify(added.element));
      console.log("tomorrow -->", added, obj);

      const tomorrow = this.$_moment().add(1, "days").format("YYYY-MM-DD");
      const url = window.tempoApiPath + "tasks.php";

      const data = new FormData();

      data.append("ID", obj.ID);
      data.append("due_date", tomorrow);

      this.$_axios
        .post(url, { params: data, operation: "updateTask" })
        .then((res) => {
          // this.refreshTasks();
          this.segmentTasks();
          console.log(res);
        })
        .catch((err) => console.log(err));
    },

    moveComing({ added }) {
      const obj = JSON.parse(JSON.stringify(added.element));
      console.log("coming -->", added, obj);

      const date = this.$_moment().add(1, "years").format("YYYY-MM-DD");
      const url = window.tempoApiPath + "tasks.php";

      const data = new FormData();

      data.append("ID", obj.ID);
      data.append("due_date", date);

      this.$_axios
        .post(url, { params: data, operation: "updateTask" })
        .then((res) => {
          // this.refreshTasks();
          this.segmentTasks();
          console.log(res);
        })
        .catch((err) => console.log(err));
    },

    moveNoDue({ added }) {
      const obj = JSON.parse(JSON.stringify(added.element));
      console.log("no due -->", added, obj);

      const url = window.tempoApiPath + "tasks.php";
      const data = new FormData();

      data.append("ID", obj.ID);
      data.append("due_date", "0000-00-00");

      this.$_axios
        .post(url, { params: data, operation: "updateTask" })
        .then((res) => {
          // this.refreshTasks();
          this.segmentTasks();
          console.log(res);
        })
        .catch((err) => console.log(err));
    },

    segmentTasks() {
      //this.tasksNoDueDate = this.$_lodash.map(this.tasks, function(o){
      //  if (o.dueDate == '0000-00-00') {
      //    return o;
      //  }
      //});

      const todaysDate = this.$_moment().format("YYYY-MM-DD");
      const tomorrowsDate = this.$_moment().add(1, "days").format("YYYY-MM-DD");

      this.tasksNoDueDate = this.$_lodash.filter(this.tasks, {
        dueDate: "0000-00-00",
      });
      this.tasksToday = this.$_lodash.filter(this.tasks, {
        dueDate: todaysDate,
      });
      this.tasksTomorrow = this.$_lodash.filter(this.tasks, {
        dueDate: tomorrowsDate,
      });
      this.tasksOther = this.$_lodash.filter(this.tasks, function (o) {
        if (
          o.dueDate != "0000-00-00" &&
          o.dueDate != todaysDate &&
          o.dueDate != tomorrowsDate
        ) {
          return o;
        }
      });

      // this.paginateTasks();
    },

    paginateTasks() {
      // paginate()
    },

    openTaskCard(item) {
      this.showModal(item);
    },

    showModal(task) {
      this.isModalVisible = true;
      this.modalTitle = "";
      this.modalTask = task;
      this.modalFooter = "Footer";
    },

    closeModal() {
      this.isModalVisible = false;
    },

    refreshTasks() {
      this.isLoading = true;

      let url =
        window.tempoApiPath +
        "tasks/read.php?assignedUserId=" +
        this.selectedAssignedUserId;
      // let url = window.tempoApiPath + "tasks.php";

      // const data = new FormData();

      // data.append("operation", "getTasks");
      // data.append(
      //   "params",
      //   JSON.stringify({ assignedUserId: this.selectedAssignedUserId })
      // );
      // data.append(
      //   "auth",
      //   JSON.stringify({
      //     username: window.tempoApiUsername,
      //     password: window.tempoApiPassword,
      //   })
      // );

      // for (let d of data) console.log(d);

      this.$_axios
        .get(url, {
          auth: {
            username: window.tempoApiUsername,
            password: window.tempoApiPassword,
          },
        })
        .then((res) => {
          console.log(res);

          this.tasks = res.data;
          this.isLoading = false;

          this.segmentTasks();
        });

      // this.$_axios.post(url, data).then((res) => {
      //   console.log(res);

      //   this.tasks = res.data;
      //   this.isLoading = false;

      //   this.segmentTasks();
      // });
    },

    filterUserChanged(selectedUserId) {
      this.selectedAssignedUserId = selectedUserId;
      this.refreshTasks();
    },

    taskTitleChanged(task) {
      //if task title is updated via modal, we also want to update the task object here so the new title will be reflected here
      //this.tasks[taskId].title = newTitle;
      //ACTUALLY - since the 'task' object is passed by reference and we modify it directly in the modal - no need to update it via emit!
    },

    taskDescriptionChanged(taskId, newDescription) {
      //if task desc is updated via modal, we also want to update the task object here so the new desc will be reflected here
      //this.tasks[taskId].description = newDescription;
      //ACTUALLY - since the 'task' object is passed by reference and we modify it directly in the modal - no need to update it via emit!
    },

    taskNewNoteAdded(taskId, newDescription) {
      //if new note is added to task, we also want to increment the note counte here
      //this.tasks[taskId].notesCount++;
    },
  },
};
</script>

<style scoped>
.tempo-modal-transition-enter-from,
.tempo-modal-transition-leave-to {
  opacity: 0;
}

.tempo-modal-transition-enter-active,
.tempo-modal-transition-leave-active {
  transition: opacity 0.1s ease;
}

.tasks-cols-container {
  display: flex;
  min-height: 500px;
  width: 100%;
  margin: 10px 0 0 0;
}

.tasks-col {
  flex-grow: 1;
  flex-basis: 0;
  width: auto;
  transition: width 1s ease-out;
  padding: 10px;
}

.tasks-col.tasks-col-collapsed {
  flex-grow: 0;
  width: 50px;
  margin: 0 3px;
  transition: width 1s ease-out;
  background-color: #cccccc;
}
.tasks-col.tasks-col-collapsed:hover {
  cursor: pointer;
}

.tasks-col.tasks-col-collapsed .tasks-col-title {
  transform: rotate(90deg);
  font-size: 15px;
  white-space: nowrap;
  padding-left: 25px;
}

.tasks-col-title:hover {
  cursor: pointer;
}

.tasks-col-title {
  font-size: 22px;
}

/*** Draggable ***/

.ghost {
  opacity: 0.5;
  /*background: #c8ebfb;*/
}
.not-draggable {
  cursor: no-drop;
}

.flip-list-move {
  transition: transform 0.5s;
}

.no-move {
  transition: transform 0s;
}
.test-rotate {
  transform: rotate(7deg);
}
</style>
