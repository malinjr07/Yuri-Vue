<template>
    <div class="input-group mt-3">
        <input
        v-model="v"
            type="text"
            class="form-control"
            placeholder="Recipient's username"
            aria-label="Recipient's username"
            aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" v-on:click='check()' type="button">Search</button>
            </div>
        </div>
    </template>

    <script>
        export default {
            name : 'FilterBar',
            methods:{
              check()
              {
                console.warn(`search button clicked ${this.v}`);
              }
            },
            data(){
              return{
                v:'',
              }
            }
        }
    </script>