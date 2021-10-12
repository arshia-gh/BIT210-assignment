import { auth, AUTH_RESOURCE } from '../API';
import sha256 from '../module/sha256';
import SimpleModel from './SimpleModel';

class User extends SimpleModel {
	constructor(username, password, email, fullName) {
		super(username);
		this.username = username;
		this.password = password;
		this.email = email;
		this.fullName = fullName;
	}

	static async hashPassword(password) {
		return await sha256(password);
	}

	static async login(username, password) {
		return auth(AUTH_RESOURCE.LOGIN, {
			username,
			password: await User.hashPassword(password)
		});
	}

	static async logout() {
		return auth(AUTH_RESOURCE.LOGOUT);
	}

	static async authenticate() {
		return auth(AUTH_RESOURCE.AUTHENTICATE);
	}
}

export default User;
