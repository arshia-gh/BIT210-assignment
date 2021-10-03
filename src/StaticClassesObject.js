// export class Batch {
//   constructor(batchNo, expiryDate, quantityAvailable, 
//   vaccine, healthcareCenter) {
//       this.batchNo = batchNo;
//       this.expiryDate = expiryDate;
//       this.quantityAvailable = quantityAvailable;
//       this.quantityAdministered = 0;
//       this.vaccine = vaccine;
//       this.healthcareCenter = healthcareCenter;
//       this.vaccinations = [];
//   }
// }

// export const batches = [
//   new Batch('PF01', '2021-03-15', 100, vaccines[0], 'ABC healthcare'),
//   new Batch('SI01', '2021-04-20', 200, vaccines[1], 'ABC healthcare'),
//   new Batch('PF02', '2021-01-12', 100, vaccines[0], 'Good healthcare'),
// ]

// export class Vaccination {
//   constructor(vaccinationID, appoinementDate, batch) {
//     this.vaccinationID = vaccinationID;
//     this.appoinementDate = appoinementDate;
//     this.batch = batch;
//   }
// }

// export const vaccinations = [
//   new Vaccination('v001', '2021-04-20', batches[0]),
//   new Vaccination('v002', '2021-01-20', batches[1]),
//   new Vaccination('v003', '2021-02-20', batches[0]),
// ]

// export class Patient {  
//   requestVaccination(appointmentDate, batch) {
//     this.vaccination = new Vaccination('v001', appointmentDate, batch);
//   }
// }
