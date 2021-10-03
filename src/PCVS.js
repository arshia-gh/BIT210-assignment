import Vaccine from './models/Vaccine.js';

// class Vaccine {
// 	constructor(vaccineID, vaccineName, manufacturer) {
// 		this.vaccineID = vaccineID
// 		this.vaccineName = vaccineName
// 		this.manufacturer = manufacturer
// 	}
// }

export const vaccines = [
    new Vaccine(1 , "Pfizer", "Pfizer Biotech Ltd"),
    new Vaccine(2 , "Sinovac", "Sinovac Biotech Ltd"),
    new Vaccine(3 , "AstraZeneca", "AstraZeneca Biotech Ltd"),
]


  