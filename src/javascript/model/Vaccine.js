import SimpleModel from './SimpleModel';
import { request, RESOURCE, METHOD } from '../API';

class Vaccine extends SimpleModel {
	#batches;
	constructor(vaccineID, vaccineName, manufacturer) {
		super(vaccineID);
		this.vaccineID = vaccineID;
		this.vaccineName = vaccineName;
		this.manufacturer = manufacturer;
		this.#batches = null;
	}

	static async create(vaccineID, vaccineName, manufacturer) {
		return await request(RESOURCE.VACCINE, {
			method: METHOD.POST,
			content: new Vaccine(vaccineID, vaccineName, manufacturer)
		});
	}

	get batches() {
		return (async () => {
			if (this.#batches == null) {
				const rows = await request(RESOURCE.VACCINATION, {
					method: METHOD.GET,
					query: { vaccineUID: this.uid }
				});
				this.#batches = [];
				rows.forEach((row) => {
					this.#batches.push(row);
				});
			}
			return this.#batches;
		})();
	}

	static fromParsedJson(obj) {
		return new Vaccine(
			obj.vaccineID,
			obj.vaccineName,
			obj.manufacturer
		);
	}
}

export default Vaccine;
