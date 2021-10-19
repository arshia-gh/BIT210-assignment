import User from './User';
import HealthcareCenter from './HealthcareCenter';
import { request, RESOURCE, METHOD } from '../API';

/**
 * Administrator model
 */
class Administrator extends User {
	#healthcareCenter;
	/**
	 * creates an Administrator object
	 *
	 * @constructs
	 * @param {string} username - admin username
	 * @param {string} password - admin password
	 * @param {string} email - admin email address
	 * @param {string} fullName - admin full name
	 * @param {HealthcareCenter} healthcareCenter - admin's responsible healthcare center
	 * @param {string} staffID - admin staff id
	 */
	constructor(username, password, email, fullName, staffID, healthcareCenterUID) {
		super(username, password, email, fullName);
		this.staffID = staffID;
		this.healthcareCenterUID = healthcareCenterUID;
		this.#healthcareCenter = null;
	}

	static async create(username, password, email, fullName, staffID, healthcareCenterUID) {
		return await request(RESOURCE.ADMINISTRATOR, {
			method: METHOD.POST,
			content: new Administrator(
				username,
				await Administrator.hashPassword(password),
				email,
				fullName,
				staffID,
				healthcareCenterUID
			)
		});
	}

	get healthcareCenter() {
		return (async () => {
			if (this.#healthcareCenter == null) {
				this.#healthcareCenter = (
					await request(RESOURCE.HEALTHCARE_CENTER, {
						query: { uid: this.healthcareCenterUID },
						method: METHOD.GET
					})
				)[0];
			}
			return this.#healthcareCenter;
		})();
	}

	static fromParsedJson(obj) {
		return new Administrator(
			obj.username,
			obj.password,
			obj.email,
			obj.fullName,
			obj.staffID,
			obj.healthcareCenterUID
		);
	}
}

export default Administrator;
