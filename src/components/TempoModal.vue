<template>
    <transition name="tempo-modal-fade">
    <div class="tempo-modal-backdrop">
        <div class="tempo-modal" role="dialog" aria-labelledby="tempoModalTitle" aria-describedby="tempoModalDescription">
            <header v-if="showHeader" class="tempo-modal-header" id="tempoModalTitle">
                <slot name="header">
                    Default header title!
                </slot>
                <button type="button" class="tempo-modal-btn-close" @click="close" aria-label="Close Modal">x</button>
            </header>
            <section class="tempo-modal-body" id="tempoModalDescription">
                <slot name="body">Default body!</slot>
            </section>
            <footer v-show="showFooter" class="tempo-modal-footer">
                <slot name="footer">default footer!</slot>
                <button type="button" class="tempo-modal-btn-green" @click="close" aria-label="Close Modal">Close me!</button>
            </footer>
        </div>
    </div>
    </transition>
</template>
<script>

//imports here

export default {
    name: 'TempoModal',
    props: {
        showHeader: {
            type: Boolean,
            default: true
        },
        showFooter: {
            type: Boolean,
            default: true
        },
    },
    components: {},
    data() {
        return {};
    },
    mounted() {},
    methods: {
        close() {
            this.$emit('close');
        }
    }
}

</script>
<style scoped>
    .tempo-modal-backdrop {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    overflow-y: auto;
  }

  .tempo-modal {
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
    text-align: left;
    overflow-y: scroll;
    position: relative;
    margin: 30px 0;
    max-height: calc(100vh - 60px);
    min-height: calc(40vh - 60px);
    max-width: 97%;
    width: 725px;
  }

  .tempo-modal-header,
  .tempo-modal-footer {
    padding: 15px;
    display: flex;
  }

  .tempo-modal-header {
    position: relative;
    border-bottom: 1px solid #eeeeee;
    /*color: #4AAE9B;*/
    /*justify-content: space-between;*/
  }

  .tempo-modal-footer {
    border-top: 1px solid #eeeeee;
    flex-direction: column;
  }

  .tempo-modal-body {
    position: relative;
    padding: 20px 20px;
  }

  .tempo-modal-btn-close {
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    color: #4AAE9B;
    background: transparent;
  }

  .tempo-modal-btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
  }

</style>