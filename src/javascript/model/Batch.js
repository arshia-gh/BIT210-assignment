import { request, RESOURCE, METHOD } from '../API';
import SimpleModel from './SimpleModel';

class Batch extends SimpleModel {
	#vaccinations;
	#vaccine;
	#healthcareCenter;

	/**
	 * creates a Batch object
	 *
	 * @constructor
	 * @param {string} batchNo - batch number
	 * @param {string} expiryDate - batch expiry date
	 * @param {string} vaccineUID -
	 * @param {string} healthcareCenterUID
	 * @param {number} quantityAvailable
	 * @param {number} quantityAdministered
	 */
	constructor(
		batchNo,
		expiryDate,
		vaccineUID,
		healthcareCenterUID,
		quantityAvailable,
		quantityAdministered = 0
	) {
		super(batchNo);
		this.batchNo = batchNo;
		this.expiryDate = expiryDate;

		// -- FOREIGN KEYS
		this.vaccineUID = vaccineUID;
		this.healthcareCenterUID = healthcareCenterUID;

		this.quantityAvailable = quantityAvailable;
		this.quantityAdministered = quantityAdministered;

		this.#vaccinations = null;
		this.#vaccine = null;
		this.#healthcareCenter = null;
	}

	static async create(
		batchNo,
		expiryDate,
		vaccineUID,
		healthcareCenterUID,
		quantityAvailable
	) {
		return await request(RESOURCE.BATCH, {
			method: METHOD.POST,
			content: new Batch(
				vaccineUID + batchNo,
				expiryDate,
				vaccineUID,
				healthcareCenterUID,
				quantityAvailable
			)
		});
	}

	async setQuantityAdministrated(newValue) {
		this.quantityAdministered = newValue;
		return await request(RESOURCE.BATCH, {
			content: { ...this },
			method: METHOD.PATCH,
			query: { uid: this.uid }
		});
	}

	async setQuantityAvailable(newValue) {
		this.quantityAvailable = newValue;
		return await request(RESOURCE.BATCH, {
			content: { ...this },
			method: METHOD.PATCH,
			query: { uid: this.uid }
		});
	}

	get vaccine() {
		return (async () => {
			if (this.#vaccine == null) {
				this.#vaccine = (
					await request(RESOURCE.VACCINE, {
						method: METHOD.GET,
						query: { uid: this.vaccineUID }
					})
				)[0];
			}
			return this.#vaccine;
		})();
	}

	get healthcareCenter() {
		return (async () => {
			if (this.#healthcareCenter == null) {
				this.#healthcareCenter = (
					await request(RESOURCE.HEALTHCARE_CENTER, {
						method: METHOD.GET,
						query: { uid: this.healthcareCenterUID }
					})
				)[0];
			}
			return this.#healthcareCenter;
		})();
	}

	get vaccinations() {
		return (async () => {
			if (this.#vaccinations == null) {
				const rows = await request(RESOURCE.VACCINATION, {
					method: METHOD.GET,
					query: { batchUID: this.uid }
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
		return new Batch(
			obj.batchNo,
			obj.expiryDate,
			obj.vaccineUID,
			obj.healthcareCenterUID,
			obj.quantityAvailable,
			obj.quantityAdministered
		);
	}
}

export default Batch;
