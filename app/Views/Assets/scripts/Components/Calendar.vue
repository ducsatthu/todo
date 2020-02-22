<template>
   <div class="container">
      <h1 class="text-center">Todo list</h1>
      <p>Author: <a href="mailto:ductranxuan.29710@gmail.com">ductranxuan</a></p>
         <v-btn dark color="primary" href="/add">
            Click here to add new work
         </v-btn>
      <vue-cal :time="false" default-view="month" :disable-views="['years']" class="calendar"/>
   </div>
</template>

<script>
    import VueCal from 'vue-cal';

    export default {
        name: 'calendar',
        components: { VueCal },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
