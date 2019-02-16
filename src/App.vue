<template>
  <div id="app">
    <div id="nav">
      <router-link to="/" class="myLink">
        <v-btn color="info">Demo</v-btn>
      </router-link>
      <router-link to="/about" class="myLink">
        <v-btn color="info">About</v-btn>
      </router-link>
    </div>
    <router-view/>
  </div>
</template>

<script  lang="ts">
import { Component, Vue, Watch } from "vue-property-decorator";
@Component
export default class App extends Vue {}
</script>

<style>
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: left;
  color: #2c3e50;
}
.myLink {
  text-decoration: none;
}
</style>
