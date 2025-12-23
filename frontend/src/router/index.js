import { createRouter, createWebHistory } from "vue-router";
import { authService } from "../services/auth.js";

// Lazy load components
const Login = () => import("../components/Login.vue");
const Register = () => import("../components/Register.vue");
const Dashboard = () => import("../components/Dashboard.vue");
const ProductsManagement = () => import("../components/ProductsManagement.vue");
const UsersManagement = () => import("../components/UsersManagement.vue");
const ImportProducts = () => import("../components/ImportProducts.vue");
const Home = () => import("../components/Home.vue");
const Products = () => import("../components/Products.vue");
const ProductDetail = () => import("../components/ProductDetail.vue");

const routes = [
  {
    path: "/",
    name: "login",
    component: Login,
    meta: { requiresAuth: false, requiresGuest: true },
  },
  {
    path: "/register",
    name: "register",
    component: Register,
    meta: { requiresAuth: false, requiresGuest: true },
  },
  // Admin routes
  {
    path: "/admin",
    name: "admin-dashboard",
    component: Dashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/products",
    name: "admin-products",
    component: ProductsManagement,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/users",
    name: "admin-users",
    component: UsersManagement,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/import",
    name: "admin-import",
    component: ImportProducts,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  // User routes
  {
    path: "/home",
    name: "home",
    component: Home,
    meta: { requiresAuth: true, requiresAdmin: false },
  },
  {
    path: "/products",
    name: "products",
    component: Products,
    meta: { requiresAuth: true, requiresAdmin: false },
  },
  {
    path: "/products/:id",
    name: "product-detail",
    component: ProductDetail,
    props: true,
    meta: { requiresAuth: true, requiresAdmin: false },
  },
  {
    path: "/cart",
    name: "cart",
    component: () => import("../components/Cart.vue"),
    meta: { requiresAuth: true, requiresAdmin: false },
  },
  // Redirect
  {
    path: "/:pathMatch(.*)*",
    redirect: "/",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = authService.isAuthenticated();
  const user = authService.getUser();
  const isAdmin = user && user.role === "admin";

  // Check if route requires authentication
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: "login" });
    return;
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && isAuthenticated) {
    // Redirect to appropriate dashboard
    if (isAdmin) {
      next({ name: "admin-dashboard" });
    } else {
      next({ name: "home" });
    }
    return;
  }

  // Check if route requires admin
  if (to.meta.requiresAdmin && !isAdmin) {
    // Redirect non-admin users to home
    next({ name: "home" });
    return;
  }

  // Check if admin tries to access user routes
  if (to.meta.requiresAuth && !to.meta.requiresAdmin && isAdmin) {
    next({ name: "admin-dashboard" });
    return;
  }

  next();
});

export default router;
