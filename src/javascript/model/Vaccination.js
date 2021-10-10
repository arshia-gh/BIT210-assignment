class Vaccination {
	constructor(vaccinationID, appoinementDate, batch) {
		this.vaccinationID = vaccinationID;
		this.appointmentDate = appoinementDate;
		this.status = 'Pending'
		this.batch = batch;
	}
}

export default Vaccination;
