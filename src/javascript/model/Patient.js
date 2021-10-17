import { request, RESOURCE, METHOD } from '../API';
import User from './User';
import Vaccination from './Vaccination';

class Patient extends User {
	#vaccinations;
	constructor(username, password, email, fullName, ICPassport) {
		super(username, password, email, fullName);
		this.ICPassport = ICPassport;
		this.#vaccinations = null;
	}

	static async create(username, password, email, fullName, ICPassport) {
		return await request(RESOURCE.PATIENT, {
			method: METHOD.POST,
			content: new Patient(
				username,
				await Patient.hashPassword(password),
				email,
				fullName,
				ICPassport
			)
		});
	}

	async createVaccination(vaccinationID, appointmentDate, batch) {
		const batchVaccinations = await this.vaccinations;
		const vaccinations = await this.vaccinations;
		const createdVaccination = await Vaccination.create(
			vaccinationID,
			appointmentDate,
			batch.uid,
			this.uid
		);
		vaccinations.push(createdVaccination);
		batchVaccinations.push(createdVaccination);
		batch.setQuantityAvailable(batch.quantityAvailable - 1);
		return createdVaccination;
	}

	get vaccinations() {
		return (async () => {
			if (this.#vaccinations == null) {
				const rows = await request(RESOURCE.VACCINATION, {
					method: METHOD.GET,
					query: { patientUID: this.uid }
				});
				this.#vaccinations = [];
				rows.forEach((row) => {
					this.#vaccinations.push(row);
				});
			}
			return this.#vaccinations;
		})();
	}

	static fromParsedJson(obj) {
		return new Patient(
			obj.username,
			obj.password,
			obj.email,
			obj.fullName,
			obj.ICPassport
		);
	}
}

export default Patient;
