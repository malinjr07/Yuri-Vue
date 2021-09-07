<template>
  <div className="task-card-compact" @click.prevent="$emit('openTask', task)">
    <!--<inline-input v-model="task.title" />-->
    <div class="task-title">{{ task.title }}</div>

    <div class="task-project-name">
      in: {{ task.companyName }} - {{ task.projectName }}
    </div>

    <div v-show="task.description.length" class="task-description">
      {{ task.description.substring(0, 100) + "..." }}
    </div>

    <div style="display: flex">
      <div style="flex: 50%" class="task-assigned-info">
        Assigned to
        <span class="task-assigned-to">
          {{ task.assignedUserNickname }}
        </span>

        <span v-show="task.assignedUserNickname != task.creatorUserNickname">
          (by
          <span class="task-assigned-by">{{ task.creatorUserNickname }}</span
          >)
        </span>
      </div>

      <div style="flex: 50%; text-align: right">
        <template v-if="isEditingDueDate">
          <span
            v-if="isEditingDueDate"
            @click.stop=""
            style="white-space: nowrap"
          >
            <input type="date" v-model="taskDueDate" @change="saveDueDate()" />
            <span
              @click="isEditingDueDate = false"
              class="task-cancel-date-change"
            >
              [x]
            </span>
          </span>
        </template>

        <template v-else>
          <span
            v-if="task.dueDate != '0000-00-00'"
            class="task-due-date"
            @click.stop="changeDueDate()"
          >
            {{ $_tempoUtils.tempoDateFormat1(taskDueDate) }}
          </span>
          <span v-else class="task-no-due-date" @click.stop="changeDueDate()">
            No due date
          </span>
        </template>
        <div v-if="task.notesCount > 0" class="task-notes-count">
          +{{ task.notesCount }}
          <span v-if="task.notesCount == 1"> Note </span>
          <span v-else> Notes </span>
        </div>
      </div>
    </div>

    <!--<button @click.prevent="$emit('complete', task)">Complete</button>-->
  </div>
</template>

<script>
//import InlineInput from 'vue-inline-input';

export default {
  props: {
    task: {
      required: true,
      type: Object,
    },
  },
  data() {
    return {
      taskDueDate: this.task.dueDate,
      isEditingDueDate: false,
    };
  },
  methods: {
    changeDueDate() {
      this.isEditingDueDate = true;
    },
    saveDueDate() {
      this.isEditingDueDate = false;
    },
  },
};
</script>


<style scoped>
.task-card-compact {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  background: white;
  transition: 0.3s;
  width: 275px;
  margin: 20px auto;
  padding: 15px 20px;
  word-wrap: break-word;
  cursor: pointer;
  text-align: left;
}

/* On mouse-over, add a deeper shadow */
.task-card-compact:hover {
  box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
}

.task-card-compact .task-title {
  font-weight: bold;
  font-size: 14px;
  margin-bottom: 4px;
}

.task-card-compact .task-project-name {
  font-style: italic;
  font-size: 12px;
  margin-bottom: 10px;
}

.task-card-compact .task-description {
  font-size: 14px;
  margin-bottom: 10px;
}

.task-card-compact .task-assigned-info {
  font-style: italic;
  font-size: 13px;
}
.task-card-compact .task-assigned-to,
.task-card-compact .task-assigned-by {
  font-weight: bold;
}

.task-card-compact .task-due-date,
.task-card-compact .task-no-due-date {
  font-size: 12px;
  text-align: right;
  text-decoration: underline dashed;
  color: #999999;
}

.task-card-compact .task-cancel-date-change {
  font-size: 12px;
  color: #999999;
}

.task-card-compact .task-notes-count {
  font-size: 12px;
  font-style: italic;
  margin: 15px 0 0 0;
}
</style>