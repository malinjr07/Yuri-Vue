import api from "./api";
import TokenService from "./token.service";

class AuthService {
  login({ username, password }) {
    const data = new FormData();

    data.append("username", username);
    data.append("password", password);

    return api
      .post("/auth/login.php", data, {
        headers: { "Content-Type": "application/json; charset=UTF-8" },
      })
      .then((response) => {
        if (response.data.jwt) {
          TokenService.setUser(response.data);
        }

        return response.data;
      });
  }

  logout() {
    TokenService.removeUser();
  }
}

export default new AuthService();
