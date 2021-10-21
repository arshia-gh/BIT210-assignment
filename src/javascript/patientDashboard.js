import { request, RESOURCE } from './API';
import main, {
	attachLogoutListener,
	fillUserData,
	toggleLoginRegister,
	toggleLogout
} from './main';
import User from './model/User';
import { renderTable } from './module/TableRenderer';
import AuthForm from './module/AuthForm';
import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', async () => {
	await main();
	const authForm = await new AuthForm((user) => {
		fillUserData(user);
		Modal.getOrCreateInstance(authForm.loginModal).hide();
		toggleLoginRegister(false);
		toggleLogout(true);
	}).init();

	attachLogoutListener('./patient.html');

	try {
		const crtUser = await User.authenticate();
		fillUserData(crtUser);
		toggleLoginRegister(false);
		toggleLogout(true);
	} catch (error) {
		toggleLoginRegister(true);
	}

	const pageTitle = document.getElementById('pageTitle');
	const pageSubtitle = document.getElementById('pageSubtitle');
	const resetButton = document.getElementById('resetButton');
	const appointmentDateForm = document.getElementById('appointmentDateForm');
	const appointmentDateFormBtn = document.getElementById('appointmentDateBtn');
	const progressBar = document.getElementById('progressBar');

	const retrievedData = {
		vaccine: await request(RESOURCE.VACCINE),
		healthcareCenter: null,
		batch: null,
		patient: null
	};

	const userSelection = {
		vaccine: null,
		healthcareCenter: null,
		batch: null,
		appointmentDate: null
	};

	const toggleAppDateForm = (show) => {
		document
			.getElementById('tableContainer')
			.classList[!show ? 'remove' : 'add']('d-none');
		appointmentDateForm.classList[show ? 'remove' : 'add']('d-none');
		appointmentDateFormBtn.classList[show ? 'remove' : 'add']('d-none');
	};

	toggleAppDateForm(false);

	appointmentDateForm.addEventListener('submit', async (event) => {
		event.preventDefault();
		userSelection.appointmentDate = appointmentDateForm['appointmentDateInp'];
		const user = await User.authenticate();
		user.createVaccination(
			Date.now().toString(),
			userSelection.appointmentDate.value,
			userSelection.batch
		);
		resetButton.innerHTML = 'Request vaccination';
		renderContent('vaccination');
	});

	const renderContent = async (toRenderTable, rowId) => {
		toggleAppDateForm(false);
		switch (toRenderTable) {
			case 'vaccines':
				progressBar.style.width = '0%';
				pageTitle.textContent = 'Vaccine';
				pageSubtitle.textContent = 'Please select a vaccine from the table below';
				return render(
					retrievedData.vaccine.map((vaccine) => ({
						vaccineID: vaccine.vaccineID,
						vaccineName: vaccine.vaccineName,
						manufacturer: vaccine.manufacturer
					})),
					['Vaccine Name', 'Manufacturer'],
					'vaccineID',
					'healthcareCenter'
				);
			case 'healthcareCenter':
				progressBar.style.width = '20%';

				pageTitle.textContent = 'Healthcare Center';
				pageSubtitle.textContent =
					'Please select a healthcare center from the table below';
				userSelection.vaccine = retrievedData.vaccine.find((vac) => vac.uid === rowId);
				retrievedData.healthcareCenter = await Promise.all(
					(
						await request(RESOURCE.BATCH, {
							query: { vaccineUID: rowId }
						})
					).map((batch) => batch.healthcareCenter)
				);
				return render(
					retrievedData.healthcareCenter,
					['Center Name', 'Center Address'],
					'uid',
					'batchAbstract'
				);
			case 'batchAbstract':
				progressBar.style.width = '40%';

				pageTitle.textContent = 'Batch';
				pageSubtitle.textContent = 'Please select a batch from the table below';
				userSelection.healthcareCenter = retrievedData.healthcareCenter.find(
					(hc) => hc.uid === rowId
				);
				const foundBatches = await request(RESOURCE.BATCH, {
					query: {
						vaccineUID: userSelection.vaccine.uid,
						healthcareCenterUID: userSelection.healthcareCenter.uid
					}
				});
				retrievedData.batch = foundBatches.filter((batch) => {
					return batch.quantityAvailable > 0;
				});
				return render(
					retrievedData.batch.map((batch) => ({
						uid: batch.uid,
						batchNo: batch.batchNo,
						vaccineName: userSelection.vaccine.vaccineName,
						healthcareCenterName: userSelection.healthcareCenter.centerName
					})),
					['Batch Number', 'Vaccine Name', 'Center Name'],
					'uid',
					'batch'
				);
			case 'batch':
				progressBar.style.width = '60%';
				userSelection.batch = retrievedData.batch.find((batch) => batch.uid === rowId);

				try {
					const crtUser = await User.authenticate();

					fillUserData(crtUser);
					return render(
						[
							{
								uid: userSelection.batch.uid,
								batchNo: userSelection.batch.batchNo,
								expiryDate: userSelection.batch.expiryDate,
								quantityAvailable: userSelection.batch.quantityAvailable
							}
						],
						['Batch Number', 'Expiry Date', 'Available Qty'],
						'uid',
						'appointmentDate'
					);
				} catch (err) {
					Modal.getOrCreateInstance(authForm.loginModal).show();
				}
				break;
			case 'appointmentDate':
				pageTitle.textContent = 'Appointment Date';
				pageSubtitle.textContent = 'Please select the appointment date';
				progressBar.style.width = '80%';
				toggleAppDateForm(true);
				break;
			case 'vaccination':
				progressBar.style.width = '0%';
				pageTitle.textContent = 'Vaccinations Status';
				pageSubtitle.textContent = 'Current status of your vaccination appointments';
				const vaccinations = await request(RESOURCE.VACCINATION, {
					query: { patientUID: (await User.authenticate()).uid }
				});
				render(
					vaccinations.map((vacs) => ({
						appointmentDate: vacs.appointmentDate,
						status: vacs.status,
						remarks: vacs.remarks
					})),
					['Appointment Date', 'Status', 'Remarks']
				);
		}
	};

	/**
	 *
	 * @param {Function} obj
	 * @param {string[]} headers
	 */
	const render = (objects, headers, key, nextToRender) => {
		renderTable(
			'tableContainer',
			objects,
			key,
			headers,
			(rowId) => renderContent(nextToRender, rowId),
			false
		);
	};

	renderContent('vaccines');
});
