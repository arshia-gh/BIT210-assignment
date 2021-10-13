import { request, RESOURCE, METHOD } from '../API';
import SimpleModel from './SimpleModel';

class Vaccination extends SimpleModel {
	#batch;
	#patient;
	constructor(
		vaccinationID,
		appointmentDate,
		status,
		remarks,
		batchUID,
		patientUID
	) {
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

	static async create(
		vaccinationID,
		appointmentDate,
		batchUID,
		patientUID
	) {
		return request(RESOURCE.VACCINATION, {
			content: new Vaccination(
				vaccinationID,
				appointmentDate,
				'pending',
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

	async setStatus(status, remarks) {
		if (
			(status === 'rejected' || status === 'accepted') &&
			remarks != null
		) {
			this.status = status;
			this.remarks = remarks || '';
			return await request(RESOURCE.VACCINATION, {
				content: { ...this },
				method: METHOD.PATCH
			});
		}
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
