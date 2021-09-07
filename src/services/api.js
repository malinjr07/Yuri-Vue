import axios from "axios";
import store from "../store";

const API = axios.create({
  //   baseURL: process.env.MIX_API_BASE_URL || '/api',
  baseURL: "https://tempodev.excitemedia.com.au/api",
  timeout: 5000,
});

API.interceptors.request.use(
  async (config) => {
    config.headers = {
      "content-type": "application/x-www-form-urlencoded",
      // Authorization: `Basic ${store.state.auth.token}`,
      ...config.headers,
    };

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default API;
