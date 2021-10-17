import Administrator from '../model/Administrator';
import Batch from '../model/Batch';
import HealthcareCenter from '../model/HealthcareCentre';
import Patient from '../model/Patient';
import Vaccination from '../model/Vaccination';
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
		'admin_test_1',
		'admin_test_1',
		'admin_test_1@email.com',
		'admin test 1',
		'test1',
		healthcareCenters[0].uid
	);
	await Administrator.create(
		'admin_test_2',
		'admin_test_2',
		'admin_test_2@email.com',
		'admin test 2',
		'test2',
		healthcareCenters[1].uid
	);
	await Administrator.create(
		'admin_test_3',
		'admin_test_3',
		'admin_test_3@email.com',
		'admin test 3',
		'test3',
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
		await Batch.create('02', '2021-01-12', vaccines[0].uid, healthcareCenters[1].uid, 100)
	];

	const patient = [
		await Patient.create(
			'patient_test_1',
			'patient_test_1',
			'patient_test_1@email.com',
			'John Banana',
			'H400100'
		),
		await Patient.create(
			'patient_test_2',
			'patient_test_2',
			'patient_test_2@email.com',
			'Papaya Tyler',
			'H400200'
		),
		await Patient.create(
			'patient_test_3',
			'patient_test_3',
			'patient_test_3@email.com',
			'Kiwi Swift',
			'H400300'
		)
	];

	const vaccinations = [
		await patient[0].createVaccination('v001', '2021-04-20', batches[0]),
		await patient[1].createVaccination('v002', '2021-01-20', batches[1]),
		await patient[2].createVaccination('v003', '2021-02-20', batches[0])
	];
};
