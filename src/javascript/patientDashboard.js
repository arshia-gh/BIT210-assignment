import { request, RESOURCE } from './API';
import main from './main';
import HealthcareCenter from './model/HealthcareCenter';
import User from './model/User';
import Vaccine from './model/Vaccine';
import { renderTable } from './module/TableRenderer';

document.addEventListener('DOMContentLoaded', async () => {
	await main();

	const tableContainerID = 'tableContainer';
	const pageTitle = document.getElementById('pageTitle');
	const pageSubtitle = document.getElementById('pageSubtitle');
	const backButton = document.getElementById('backButton');

	const vaccines = await request(RESOURCE.VACCINE);

	const currentSelection = {
		vaccine: await request(RESOURCE.VACCINATION),
		healthcareCenter: null,
		batch: null,
		patient: null
	};

	const renderContent = (toRenderTable, rowID) => {
		switch (toRenderTable) {
			case 'vaccines':
				return render(currentSelection.vaccine, ['Vaccine Name', 'Manufacturer']);
			case 'healthcareCenter':
		}
	};

	/**
	 *
	 * @param {Function} obj
	 * @param {string[]} headers
	 */
	const render = (obj, headers) => {
		const filteredObj = oObject.keys(obj).reduce((prev, crt) => {
			if (obj[crt] !== obj.uid && crt !== 'uid') prev[key] = obj[key];
			return prev;
		}, {});
		renderTable(
			'tableContainer',
			filteredObj,
			'uid',
			headers,
			(rowId) => renderContent(obj.name, rowId),
			false
		);
	};

	/**
	 * @param {Vaccine[]} vaccines
	 */
	const renderVaccines = (vaccines) => {
		pageTitle.textContent = 'Vaccines';
		pageSubtitle.textContent = 'Please select a vaccine from the table below';
		renderTable(
			tableContainerID,
			vaccines.map(({ vaccineID, vaccineName, manufacturer }) => ({
				vaccineID,
				vaccineName,
				manufacturer
			})),
			'vaccineID',
			['vaccineID', 'Vaccine name', 'Manufacturer'],
			async (rowID) => {
				newVaccination.vaccineUID = rowID;
				healthcareCenters = await Promise.all(
					(
						await request(RESOURCE.BATCH, {
							query: { vaccineUID: rowID }
						})
					).map(async (batch) => await batch.healthcareCenter)
				);
				renderHealthcareCenters(healthcareCenters);
				backButton.dataset.prevTable = 'vaccines';
			},
			false
		);
	};

	/**
	 *
	 * @param {HealthcareCenter[]} healthcareCenters
	 */
	const renderHealthcareCenters = (healthcareCenters) => {
		pageTitle.textContent = 'Healthcare Centers';
		pageSubtitle.textContent = 'Please select a healthcare center from the table below';
		renderTable(
			tableContainerID,
			healthcareCenters.map(({ centerName, address }) => ({
				centerName,
				address
			})),
			'centerName',
			['Center name', 'Center address'],
			async (rowID) => {
				batches = await request(RESOURCE.BATCH, {
					query: { vaccineUID: newVaccination.vaccineUID, healthcareCenterUID: rowID }
				});
				renderBatches(
					batches,
					healthcareCenters.find(({ uid }) => uid === rowID).centerName
				);
				backButton.dataset.prevTable = 'healthcareCenters';
			}
		);
	};

	const renderBatches = async (batches, healthcareCenterName) => {
		pageTitle.textContent = 'Batches';
		pageSubtitle.textContent = 'Please select a batch from the table below';
		renderTable(
			tableContainerID,
			batches.map((batch) => ({
				batchNo: batch.batchNo,
				batches: healthcareCenterName
			})),
			'batchNo',
			['Batch Number', 'Center Name'],
			async (rowID) => {
				newVaccination.batchUID = rowID;
				try {
					currentUser = User.authenticate();
					newVaccination.patientUID = currentUser.uid;
				} catch (e) {}
				backButton.dataset.prevTable = 'healthcareCenters';
			}
		);
	};

	renderVaccines(vaccines);
});
