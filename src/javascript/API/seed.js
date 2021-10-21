import Administrator from '../model/Administrator';
import Batch from '../model/Batch';
import HealthcareCenter from '../model/HealthcareCenter';
import Patient from '../model/Patient';
import Vaccine from '../model/Vaccine';

export default async () => {
	// Healthcare Centers
	const healthcareCenters = [
		await HealthcareCenter.create(
			'Century Medical Centre',
			'55-57, Jalan SS 25/2, Taman Mayang, 47301 Petaling Jaya, Selangor'
		),
		await HealthcareCenter.create(
			'Klinik Impian Care 24 jam',
			'3g, Tingkat bawah, Jalan Bunga Cempaka 6a, Taman Muda, 68000 Ampang, Selangor'
		),
		await HealthcareCenter.create(
			'Healthcare Dialysis Centre Sdn Bhd',
			'41, Jalan 6/31, Seksyen 6, 46000 Petaling Jaya, Selangor'
		)
	];

	// Administrators
	await Administrator.create(
		'Arshia',
		'Arshia',
		'Arshia@email.com',
		'Arshia',
		'H100100',
		healthcareCenters[0].uid
	);
	await Administrator.create(
		'Carrick',
		'Carrick',
		'carrick@email.com',
		'Carrick',
		'H900600',
		healthcareCenters[1].uid
	);
	await Administrator.create(
		'Michael_Wijaya',
		'Michael_Wijaya',
		'michael_wijaya@email.com',
		'Michael Wijaya',
		'H900300',
		healthcareCenters[2].uid
	);

	// Vaccines
	const vaccines = [
		await Vaccine.create('PF', 'Pfizer', 'Pfizer Biotech Ltd'),
		await Vaccine.create('SI', 'Sinovac', 'Sinovac Biotech Ltd'),
		await Vaccine.create('AS', 'AstraZeneca', 'AstraZeneca Biotech Ltd')
	];
	const batches = [
		await Batch.create(
			'01',
			'2021-03-15',
			vaccines[0].uid,
			healthcareCenters[0].uid,
			100
		),
		await Batch.create(
			'03',
			'2021-04-20',
			vaccines[1].uid,
			healthcareCenters[0].uid,
			200
		),
		await Batch.create(
			'02',
			'2021-01-12',
			vaccines[0].uid,
			healthcareCenters[1].uid,
			100
		),
		await Batch.create('20', '2021-01-12', vaccines[0].uid, healthcareCenters[0].uid, 100)
	];

	const patient = [
		await Patient.create(
			'John_Banana',
			'John_Banana',
			'johnnanana@email.com',
			'John Banana',
			'H400100'
		),
		await Patient.create(
			'Papaya_Tyler',
			'Papaya Tyler',
			'papayatyler@email.com',
			'Papaya Tyler',
			'H400200'
		),
		await Patient.create(
			'Kiwi_Swift',
			'Kiwi Swift',
			'kiwiswift@email.com',
			'Kiwi Swift',
			'H400300'
		)
	];

	const vaccinations = [
		await patient[0].createVaccination('1634666824687', '2021-04-20', batches[0]),
		await patient[1].createVaccination('5353435852450', '2021-01-20', batches[1]),
		await patient[2].createVaccination('8764646876876', '2021-02-20', batches[0])
	];
};
