const changesAppliedBadge = document.getElementById("changesAppliedBadge");
const tableContainer = document.getElementById("tableContainer");
const vaccinationTable = tableContainer.querySelector("table");
const vaccinationDetailContainer = document.getElementById(
  "vaccinationDetailContainer"
);
const manageVaccinationModal = document.getElementById(
  "manageVaccinationModal"
);
const modalObj = new bootstrap.Modal(manageVaccinationModal);

const batchNo = new URLSearchParams(window.location.search).get("batchNo");

// statusButtonGroup.onchange = () => {
//   const isAccepting = rdbAccept.checked;
//   remarksInput.disabled = isAccepting;
//   btnSubmit.innerHTML = (isAccepting ? "Confirm" : "Reject") + " Appointment";
// };

// function showStatusGroup(show = true) {
//   //show or hide the button group
//   statusButtonGroup.style.display = show ? "inline-flex" : "none";
//   rdbAccept.required = show;
// }

vaccinationTable.onclick = (e) => {
  if (e.target.parentNode.parentNode.tagName === "THEAD") return; //do nothing if it is the table header cell

  const tr = e.target.parentNode;
  const vaccinationID = tr.getAttribute("data-row-id");
  if (vaccinationID) window.location = 'manage-vaccination.php?vaccinationID=' + vaccinationID;


  //retrieve the html body of the vaccination details
  // fetch("./vaccination-modal.php?vaccinationID=" + vaccinationID, {
  //   headers: { "Content-Type": "text/html" },
  // })
  //   .then((vaccinationDetails) => vaccinationDetails.text())
  //   .then((htmlText) => {
  //     //response as html elements
  //     console.log(htmlText);
  //     const parser = new DOMParser();
  //     const html = parser.parseFromString(htmlText, "text/html").firstChild;
  //     console.log(html.firstChild);

  //     // alert(typeof htmlDoc);
  //     vaccinationDetailContainer.replaceChildren(html);

  //     let x = html.querySelector('script');
  //     eval(x.innerHTML);
  //     // const details = vaccinationDetailContainer.childNodes[1];
  //     // const status = details.getAttribute("data-status");
  //     // manageVaccinationModal.setAttribute("data-vaccinationID", vaccinationID);
  //     // updateControlByStatus(status);
  //     modalObj.show();
  //   });
};

function updateControlByStatus(status) {
  switch (status) {
    case "pending": //show approval form
      showStatusGroup();
      btnSubmit.innerHTML = "Update Status";
      break;
    case "confirmed": //show confirmation for administer
      showStatusGroup(false);
      statusButtonGroup.style.display = "none";
      statusButtonGroup.disabled = "true";
      btnSubmit.innerHTML = "Confirm Administered";
      remarksInput.disabled = false;
      break;
    case "rejected":
    case "administered": //show nothing but disabled remarks
      showStatusGroup(false);
      statusButtonGroup.style.display = btnSubmit.style.display = "none";
      remarksInput.value = vaccination.remarks ?? "No remarks was recorded.";
      remarksInput.disabled = true;
      break;
  }
}

function showChangesApplied() {
  //show the badge and dismiss after 2 seconds
  changesAppliedBadge.classList.remove("d-none");

  setTimeout(() => {
    changesAppliedBadge.classList.add("d-none");
  }, 2000);
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
