import Vue from "vue";
import Router from "vue-router";
import Demo from "./views/Demo.vue";

Vue.use(Router);

export default new Router({
  mode: "history",
  base: process.env.BASE_URL,
  routes: [
    {
      path: "/",
      name: "demo",
      component: Demo
    },
    {
      path: "/about",
      name: "about",
      // route level code-splitting
      // this generates a separate chunk for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("./views/About.vue")
    },
    {
      path: "*",
      redirect: "/"
    }
  ]
});
