import { request, RESOURCE, METHOD } from '../API';
import SimpleModel from './SimpleModel';

const STATUS = {
	PENDING: 'pending',
	REJECTED: 'rejected',
	CONFIRMED: 'confirmed',
	ADMINISTERED: 'administered'
};

class Vaccination extends SimpleModel {
	#batch;
	#patient;
	constructor(vaccinationID, appointmentDate, status, remarks, batchUID, patientUID) {
		super(vaccinationID);
		this.vaccinationID = vaccinationID;
		this.appointmentDate = appointmentDate;
		this.status = status;
		this.remarks = remarks;

		// -- FOREIGN KEYS
		this.batchUID = batchUID;
		this.patientUID = patientUID;

		this.#batch = null;
		this.#patient = null;
	}

	static async create(vaccinationID, appointmentDate, batchUID, patientUID) {
		return request(RESOURCE.VACCINATION, {
			content: new Vaccination(
				vaccinationID,
				appointmentDate,
				STATUS.PENDING,
				null,
				batchUID,
				patientUID
			),
			method: METHOD.POST
		});
	}

	get batch() {
		return (async () => {
			if (this.#batch == null) {
				this.#batch = (
					await request(RESOURCE.BATCH, {
						method: METHOD.GET,
						query: { uid: this.batchUID }
					})
				)[0];
			}
			return this.#batch;
		})();
	}

	get patient() {
		return (async () => {
			if (this.#patient == null) {
				this.#patient = (
					await request(RESOURCE.PATIENT, {
						method: METHOD.GET,
						query: { uid: this.patientUID }
					})
				)[0];
			}
			return this.#patient;
		})();
	}

	async setStatus(status, remarks = '') {
		if (
			(this.status === STATUS.CONFIRMED && status === STATUS.ADMINISTERED) ||
			([STATUS.CONFIRMED, STATUS.REJECTED].includes(status) &&
				this.status === STATUS.PENDING)
		) {
			if (status === STATUS.CONFIRMED) {
				remarks = '';
			}
			if (remarks == null) remarks = '';
			const updatedObj = await request(RESOURCE.VACCINATION, {
				content: { ...this, status, remarks },
				method: METHOD.PATCH,
				query: { uid: this.uid }
			});

			this.status = updatedObj.status;
			this.remarks = updatedObj.remarks;

			// update the batch
			const batch = await this.batch;
			if (this.status === STATUS.ADMINISTERED) {
				await batch.setQuantityAdministrated(batch.quantityAdministered + 1);
			} else if (this.status === STATUS.REJECTED) {
				await batch.setQuantityAvailable(batch.quantityAvailable + 1);
			}
		}
		return this;
	}

	static fromParsedJson(obj) {
		return new Vaccination(
			obj.vaccinationID,
			obj.appointmentDate,
			obj.status,
			obj.remarks,
			obj.batchUID,
			obj.patientUID
		);
	}
}

export default Vaccination;
