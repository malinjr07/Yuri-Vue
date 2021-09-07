import { createWebHistory, createRouter } from "vue-router";
import TokenService from "../services/token.service";
import Home from "../pages/Home.vue";
import Tasks from "../pages/Tasks.vue";
import Queues from "../pages/Queues.vue";
import Projects from "../pages/Projects.vue";
import Leads from "../pages/Leads.vue";
import Login from "../pages/Login.vue";

const user = TokenService.getUser();

const routes = [
  {
    path: "/",
    name: "home",
    component: Home,
  },
  {
    path: "/login",
    name: "login",
    component: Login,
  },
  {
    path: "/tasks",
    name: "tasks",
    component: Tasks,
  },
  {
    path: "/queues",
    name: "queues",
    component: Queues,
  },
  {
    path: "/projects",
    name: "projects",
    component: Projects,
  },
  {
    path: "/leads",
    name: "leads",
    component: Leads,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
