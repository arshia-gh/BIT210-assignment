const tableContainer = document.getElementById('tableContainer');
const vaccinationTable = tableContainer.querySelector("table");
const vaccinationDetailContainer = document.getElementById('vaccinationDetailContainer');
const manageVaccinationModal = document.getElementById('manageVaccinationModal');
const modalObj = new bootstrap.Modal(manageVaccinationModal);

vaccinationTable.addEventListener('click', (e) => {
    const tr = e.target.parentNode;
    const vaccinationID = tr.getAttribute('data-row-id');

    //retrieve the html body of the vaccination details
    fetch('./vaccination-details.php?vaccinationID=' + vaccinationID, 
    {headers: { 'Content-Type': 'text/html'}})
    .then(vaccinationDetails => vaccinationDetails.text())
    .then(html => { //response as html elements
        vaccinationDetailContainer.innerHTML = html;
        modalObj.show(); 
    });
})

function updateControlByStatus() {
    
}



// function showManageVaccinationModal(vaccination) {
// 	// updateModalWith(vaccination);
//     console.log(vaccination);
// 	modalObject.show();
// }

// function updateModalWith(vaccination) {
// 	manageVaccinationForm.reset();
// 	manageVaccinationModal.setAttribute('data-vaccinationID', vaccination.vaccinationID);

// 	const patientTbody = document.getElementById('patient-info-tbody');
// 	const rawData = await request(RESOURCE.PATIENT, {
// 		query: { username: vaccination.patientUID }
// 	});
// 	const patient = await rawData[0];

// 	const patientInfo = {
// 		'Full Name': patient.fullName,
// 		'IC/Passport': patient.ICPassport
// 	};

// 	fillTbody(patientTbody, patientInfo);

// 	const batchTbody = document.getElementById('batch-info-tbody');

// 	const batchInfo = {
// 		'Batch Number': batch.batchNo,
// 		'Expiry Date': batch.expiryDate,
// 		'Vaccine Name': (await batch.vaccine).vaccineName,
// 		Manufacturer: (await batch.vaccine).manufacturer
// 	};

// 	fillTbody(batchTbody, batchInfo);

// 	btnSubmit.style.display = 'block';

// 	switch (vaccination.status) {
// 		case 'pending': //show approval form
// 			showStatusGroup();
// 			btnSubmit.innerHTML = 'Update Status';
// 			operationTitle.innerHTML = 'Approval of Appointment';
// 			break;
// 		case 'confirmed': //show confirmation for administer
// 			showStatusGroup(false);
// 			statusButtonGroup.style.display = 'none';
// 			statusButtonGroup.disabled = 'true';
// 			btnSubmit.innerHTML = 'Confirm Administered';
// 			operationTitle.innerHTML = 'Confirm Vaccination Administered';
// 			remarksInput.disabled = false;
// 			break;
// 		case 'rejected':
// 		case 'administered': //show nonthing but disabled remarks
// 			showStatusGroup(false);
// 			statusButtonGroup.style.display = btnSubmit.style.display = 'none';
// 			remarksInput.value = vaccination.remarks
// 				? vaccination.remarks
// 				: 'No remarks was recorded.';
// 			operationTitle.innerHTML = `This vaccination has been ${vaccination.status}.`;
// 			remarksInput.disabled = true;
// 			break;
// 	}
// }