import { request, RESOURCE } from './API';
import main, { fillUserData } from './main';
import User from './model/User';
import { renderTable } from './module/TableRenderer';
import AuthForm from './module/AuthForm';
import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', async () => {
	await main();
	const authForm = await new AuthForm((user) => {
		fillUserData(crtUser);
	}).init();
	await fillUserData();

	const logoutBtn = document.getElementById('logoutBtn');
	logoutBtn.addEventListener('click', async () => {
		await User.logout();
		window.location.reload();
	});

	const pageTitle = document.getElementById('pageTitle');
	const pageSubtitle = document.getElementById('pageSubtitle');
	const backButton = document.getElementById('backButton');

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

	const renderContent = async (toRenderTable, rowId) => {
		switch (toRenderTable) {
			case 'vaccines':
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
				try {
					const crtUser = await User.authenticate();
					fillUserData(crtUser);
				} catch (err) {
					Modal.getOrCreateInstance(authForm.loginModal).show();
				}
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
