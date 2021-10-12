import { request, RESOURCE, METHOD } from '../API';
import Administrator from './Administrator';
import Batch from './Batch';
import SimpleModel from './SimpleModel';

/**
 * HealthcareCenter model
 */
class HealthcareCenter extends SimpleModel {
	#administrators;
	#batches;
	/**
	 *
	 * @param {string} centerName
	 * @param {string} address
	 */
	constructor(centerName, address) {
		super(centerName.replace(/\s/, '-'));
		this.centerName = centerName;
		this.address = address;
		this.#administrators = null;
		this.#batches = null;
	}

	static async create(centerName, address) {
		return await request(RESOURCE.HEALTHCARE_CENTER, {
			method: METHOD.POST,
			content: new HealthcareCenter(centerName, address)
		});
	}

	async createAdministrator(
		username,
		password,
		email,
		fullName,
		staffID
	) {
		const administrators = await this.administrators;
		const createdAdmin = await Administrator.create(
			username,
			password,
			email,
			fullName,
			staffID,
			this.uid
		);
		administrators.push(createdAdmin);
		return createdAdmin;
	}

	get administrators() {
		return (async () => {
			if (this.#administrators == null) {
				const rows = await request(RESOURCE.ADMINISTRATOR, {
					method: METHOD.GET,
					query: { healthcareCenterUID: this.uid }
				});
				this.#administrators = [];
				rows.forEach((row) => {
					this.#administrators.push(row);
				});
			}
			return this.#administrators;
		})();
	}

	async createBatch(batchNo, expiryDate, vaccine, quantityAvailable) {
		const vaccineBatches = await vaccine.batches;
		const batches = await this.batches;
		const createdBatch = await Batch.create(
			batchNo,
			expiryDate,
			vaccine.uid,
			this.uid,
			quantityAvailable
		);
		batches.push(createdBatch);
		vaccineBatches.push(createdBatch);
		return createdBatch;
	}

	get batches() {
		return (async () => {
			if (this.#batches == null) {
				const rows = await request(RESOURCE.BATCH, {
					method: METHOD.GET,
					query: { healthcareCenterUID: this.uid }
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
		return new HealthcareCenter(obj.centerName, obj.address);
	}
}

export default HealthcareCenter;
