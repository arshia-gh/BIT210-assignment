import Vaccine from './models/Vaccine'
import Batch from './models/Batch'
import Vaccination from './models/Vaccination'

export const vaccines = [
	new Vaccine(1, 'Pfizer', 'Pfizer Biotech Ltd'),
	new Vaccine(2, 'Sinovac', 'Sinovac Biotech Ltd'),
	new Vaccine(3, 'AstraZeneca', 'AstraZeneca Biotech Ltd'),
]

export const batches = [
	new Batch('PF01', '2021-03-15', 100, vaccines[0], 'ABC healthcare'),
	new Batch('SI01', '2021-04-20', 200, vaccines[1], 'ABC healthcare'),
	new Batch('PF02', '2021-01-12', 100, vaccines[0], 'Good healthcare'),
]

export const vaccinations = [
	new Vaccination('v001', '2021-04-20', batches[0]),
	new Vaccination('v002', '2021-01-20', batches[1]),
	new Vaccination('v003', '2021-02-20', batches[0]),
]
