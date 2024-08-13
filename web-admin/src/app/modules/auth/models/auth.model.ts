export class AuthModel {
  token: string;
  refresh_token: string;
  expiresIn?: Date;

  setAuth(auth: AuthModel) {
    this.token = auth.token;
    this.refresh_token = auth.refresh_token;
    this.expiresIn = auth.expiresIn;
  }
}
