import API, { METHOD, RESOURCE } from './API';
import Vaccines from './model/Vaccine';

function x() {
	API.request(METHOD.GET, RESOURCE.VACCINES).then((vaccines) => {
		console.log(vaccines);
		function AddNewVaccine() {
			const modal = document.getElementById('addNewVaccineModal');
			const form = document.querySelector('form');

			const vaccineNameInput = document.getElementById(
				'vaccineNameInput'
			);
			const manufacturerInput = document.getElementById(
				'manufacturerInput'
			);
			const vaccineName = vaccineNameInput.value;
			const manufacturer = manufacturerInput.value;

			if (vaccineName && manufacturer) {
				const newVaccine = new Vaccines(
					getNextVaccineID(),
					vaccineName,
					manufacturer
				);
				vaccines.push(newVaccine);
				AppendVaccineToTable(newVaccine);

				alert(
					`${vaccineName} made by ${manufacturer} has been added`
				);
				form.reset();
			}
			// if(vaccines.find)
		}

		const getNextVaccineID = () =>
			Math.max(...vaccines.map((vaccine) => vaccine.vaccineID)) + 1;

		const vaccineTable = document.getElementById('vaccine-table');

		const AppendVaccineToTable = (vaccine) => {
			let tr = document.createElement('tr');
			tr.setAttribute('role', 'button');
			tr.setAttribute('data-vaccineID', vaccine.vaccineID);

			for (let prop in vaccine) {
				let td = document.createElement('td');
				td.innerHTML = vaccine[prop];
				tr.appendChild(td);
			}

			vaccineTable.appendChild(tr);
		};

		vaccines.forEach((vaccine) => AppendVaccineToTable(vaccine));

		vaccineTable.addEventListener('click', (e) => {
			let tr = e.target.parentNode;
			let vaccineID = +tr.getAttribute('data-vaccineID');

			if (vaccineID) {
				let selectedVaccine = vaccines.find(
					(vaccine) => vaccine.vaccineID === vaccineID
				);

				if (selectedVaccine) showAddBatchModal(selectedVaccine);
			}
		});

		function showAddBatchModal(vaccine) {
			const modal = new bootstrap.Modal(
				document.getElementById('addNewBatchModal'),
				{}
			);
			document.getElementById(
				'addNewBatchModalLabel'
			).innerHTML = `${vaccine.vaccineName} | New Batch`;
			modal.show();
		}
	});
}

export default x;
