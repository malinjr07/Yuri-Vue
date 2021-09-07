<template>
    
    <TempoModal v-bind:key="task.ID" v-bind:task="task" :showHeader="true" :showFooter="false">
      <template v-slot:header>
        Task Deets [ID {{ task.ID }}]
      </template>
      <template v-slot:body >
        <div class="tempo-task-card-body">
            <InlineEditField class="task-card-title" :fieldValue="task.title" @fieldUpdated="saveTitle"></InlineEditField>
            <div class="task-card-project-name">in {{ task.companyName }} - {{ task.projectName }}</div>
            <div class="task-card-assigned-info">Assigned to <span class="task-card-assigned-to">{{ task.assignedUserNickname }}</span><span v-show="task.assignedUserNickname != task.creatorUserNickname"> (by <span class="task-card-assigned-by">{{ task.creatorUserNickname }}</span>)</span></div>
            <h2>Description <button v-if="!isEditingDescription" @click="isEditingDescription = true">Edit</button></h2>
            <div class="task-card-description" v-show="!isEditingDescription" @click="isEditingDescription = true" v-html="markedDescriptionToHtml"></div>
            <template v-if="isEditingDescription">
                <div><textarea :value="taskDescription" ref="taskDescription" class="task-card-description-editor"></textarea></div>
                <div style="display: flex;">
                    <div style="flex: 50%;">
                        <button @click="saveDescription()">Save Description</button> <span class="task-cancel-description-change" @click="cancelDescriptionEdit()">[x]</span>
                    </div>
                    <div style="text-align: right;flex: 50%;">
                        <span class="want-formatting" @click.prevent="showMarkdownHelp = !showMarkdownHelp">Want formatting?</span>
                    </div>
                </div>
                <div v-show="showMarkdownHelp" class="want-formatting-desc">Use bold, italics, heading, bullets (and more) but learning simple 'markdown' options. Check out the <a class="want-formatting-link" href="https://www.markdownguide.org/cheat-sheet/" target="_blank">cheat sheet!</a></div>
            </template>
            <div class="task-notes-container">
                <div class="task-new-note-container" :class="{ 'task-adding-note-active': isAddingNote }">
                    <textarea class="task-new-note-text" :value="newNoteText" ref="newNoteText" placeholder="New Note..." @click="handleNewNoteTextClick($event.target.value)" @blur="handleNewNoteTextBlur($event.target.value)"></textarea>
                    <template v-if="isAddingNote">
                        <textarea class="task-new-note-additional-text" :value="newNoteAdditionalText" ref="newNoteAdditionalText" placeholder="Additional Text..."></textarea>
                        <button @click="saveNewNote()">Save Note</button> <span class="task-cancel-note-add" @click="cancelNoteAdd()">[x]</span>
                    </template>
                </div>
                <div v-if="taskNotes.length > 0">
                    <div v-for="note in taskNotes" :key="note.ID" class="task-note-container">
                        <div class="task-note-datetime">{{ $_tempoUtils.tempoDateTimeFormat1(note.noteDateTime) }} <span class="task-note-created-by">(by {{ note.creatorUserNickname }})</span></div>
                        <div class="task-note-text"><strong>Note Text:</strong> {{ note.noteText}}</div>
                        <div class="task-note-additional-text"><strong>Additional Text:</strong> {{ note.noteAdditionalText}}</div>
                    </div>
                </div>
            </div>
        </div>
      </template>
      <template v-slot:footer></template>

    </TempoModal>
</template>
<script>

import TempoModal from '../components/TempoModal.vue'
import InlineEditField from '../components/InlineEditField.vue'
import marked from 'marked'

//import axios from 'axios'


export default {
    components: {
        TempoModal,
        InlineEditField
        
    },
    emits: ['taskTitleChanged','taskNewNoteAdded'],
    props: {
        task: {
            required: true,
            type: Object
        }
    },
    computed: {
        markedDescriptionToHtml() {
            return marked(this.taskDescription)
        }

    },
    methods: {
        saveNewNote() {
            this.newNoteText = this.$refs['newNoteText'].value;
            this.newNoteAdditionalText = this.$refs['newNoteAdditionalText'].value;

            let url = window.tempoApiPath + "notes/create.php";
            const payload = { noteText: this.newNoteText, noteAdditionalText: this.newNoteAdditionalText, relatedTable: 'tasks', relatedTableId: this.task.ID, creatorUserId: 4 };

            this.$_axios.post(url, payload, {auth: {
                username: window.tempoApiUsername,
                password: window.tempoApiPassword
            }})
            .then(response => {
                this.updateResponse = response.data;
                this.$emit('taskNewNoteAdded', this.task.ID);
                this.task.notesCount++;
                })


            this.isAddingNote = false;
            this.newNoteText = '';
            this.newNoteAdditionalText = '';
            this.getTaskNotes();
            return true;

        },
        cancelNoteAdd() {

            
            
            if(this.$refs['newNoteText'].value != '' && confirm('Are you sure you want to discard this note?')) {
            this.isAddingNote = false;
            }
            else {
                return true;
            }
            
            
        },
        handleNewNoteTextClick(fieldValue) {
            if(fieldValue == '') {
                this.isAddingNote = true;
            }
        },
        handleNewNoteTextBlur(fieldValue) {
            if(fieldValue == '') {
                this.isAddingNote = false;
            }
        },
        saveTitle(newTitle) {
            console.log('saving title: ' + newTitle);
            //this.taskTitle = newTitle;
            this.task.title = newTitle;

            let url = window.tempoApiPath + "tasks/update.php";
            const payload = { title: this.task.title, description: this.taskDescription, ID: this.task.ID };

            this.$_axios.post(url, payload, {auth: {
                username: window.tempoApiUsername,
                password: window.tempoApiPassword
            }})
            .then(response => {
                this.updateResponse = response.data;
                //this.$emit('taskTitleChanged',  this.task.ID, newTitle)
                this.$emit('taskTitleChanged',  this.task)
            })

        },
        saveDescription() {

            //copy the new value to the js var. We are only using one way binding (v-bind instead of v-model) because we want to allow the user to "cancel" their changes if they want to.
            this.taskDescription = this.$refs['taskDescription'].value;

            let url = window.tempoApiPath + "tasks/update.php";
            const payload = { title: this.task.title, description: this.taskDescription, ID: this.task.ID };

            this.$_axios.post(url, payload, {auth: {
                username: window.tempoApiUsername,
                password: window.tempoApiPassword
            }})
            .then(response => {
                this.updateResponse = response.data;
                this.$emit('taskDescriptionChanged', this.task.ID, this.taskDescription);
                })


            this.isEditingDescription = false;
            return true;
        },
        cancelDescriptionEdit() {

            if(confirm('Are you sure you want to discard your changes?')) {
                this.isEditingDescription = false;
            }
            else {
                return true;
            }
        },
        getTaskNotes() {
            let url = window.tempoApiPath + "notes/read.php?relatedTable=tasks&relatedTableId=" + this.task.ID;

            this.$_axios
                .get(url, {auth: {
                username: window.tempoApiUsername,
                password: window.tempoApiPassword
                }})
                .then(
                    response => {
                    this.taskNotes = response.data;

                    //sort the notes
                    //this.taskNotes = this.$_lodash.orderBy(this.taskNotes, ['noteDateTime'], ['desc'])
                    //this.isLoading = false;

                    }
                    )

        }
    },
    data() {
        return {
            //taskDescription: this.$_lodash.cloneDeep(this.task.description),
            taskDescription: this.task.description,
            //taskTitle: this.task.title,
            updateResponse: null,
            isEditingDescription: false,
            isAddingNote: false,
            showMarkdownHelp: false,
            newNoteText: '',
            newNoteAdditionalText: '',
            taskNotes: []

        }
    },
    mounted() {
        this.getTaskNotes();
    }
    
    
}

</script>
<style scoped>

.tempo-task-card-body .task-card-project-name {
    font-style: italic;
    font-size: 13px;
}
.tempo-task-card-body .task-card-description {
    white-space: pre-line;
}
.tempo-task-card-body {
    width: 600px;
}
.tempo-task-card-body h1 {
    font-size: 16px;
    font-weight: bold;
    margin: 10px 0;
    padding: 0;
}
.tempo-task-card-body h2 {
    font-size: 14px;
    font-weight: bold;
}
.tempo-task-card-body .task-card-title >>> input {
    width: 100%;
    border-radius: 3px;
    padding: 6px 5px;
    font-size: 14px;

}
.tempo-task-card-body .task-card-title {
    font-weight: bold;
    font-size: 16px;
}
.tempo-task-card-body .task-card-description:hover {
    cursor: pointer;
}
.tempo-task-card-body .task-card-description-editor {
    width: 100%;
    min-height: 400px;
}

.tempo-task-card-body .task-card-assigned-info {
    font-style: italic;
    font-size: 13px;
    margin-top: 15px;
}
.tempo-task-card-body .task-card-assigned-to, .tempo-task-card-body .task-card-assigned-by {
    font-weight: bold;
}
.tempo-task-card-body .want-formatting {
    font-size: 11px;
    text-decoration: underline;
}
.tempo-task-card-body .want-formatting:hover {
    cursor: pointer;
}
.tempo-task-card-body .want-formatting-desc {
    font-size: 12px;
    margin: 10px 0;
}
.tempo-task-card-body .task-cancel-description-change, .tempo-task-card-body .task-cancel-note-add {
    font-size: 12px;
    color: #999999;
    cursor: pointer;
}
.tempo-task-card-body .task-notes-container {
    margin: 20px 0 0 0;
}
.tempo-task-card-body .task-note-container {
    background-color: #EEEEEE;
    border-radius: 8px;
    padding: 10px;
    margin: 9px 0;
    font-size: 13px;
    line-height: 17px;
}

.tempo-task-card-body .task-note-container .task-note-datetime {
    font-weight: bold;
    font-size: 12px;
    margin-bottom: 12px;
}

.tempo-task-card-body .task-note-container .task-note-created-by {
    font-weight: normal;
    font-style: italic;
}

.tempo-task-card-body .task-new-note-text, .tempo-task-card-body .task-new-note-additional-text {
    width: 100%;
    height: 40px;
}

.tempo-task-card-body .task-adding-note-active .task-new-note-text {
    height: 70px;
}



</style>
