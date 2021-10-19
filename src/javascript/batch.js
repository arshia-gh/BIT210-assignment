import main, { fillUserData } from './main';
import { Modal } from "bootstrap";
import { request, RESOURCE, AUTH_RESOURCE, auth } from "./API";
import { renderTable } from "./module/TableRenderer";

let batch, vaccinations;

const batchInfoContainer = document.getElementById('batchInfoContainer');

const manageVaccinationForm = document.getElementById('manageVaccinationForm');
const manageVaccinationModal = document.getElementById("manageVaccinationModal");
const modalObject = new Modal(manageVaccinationModal);

const statusButtonGroup = document.getElementById('statusButtonGroup');
const rdbAccept = document.getElementById('rdbAccept');
const remarksInput = document.getElementById('remarks-input');
const btnSubmit = document.getElementById('submitButton');
const operationTitle = document.getElementById('operationTitle');
const changesAppliedBadge = document.getElementById('changesAppliedBadge');

const getModalVaccinationID = _ => manageVaccinationModal.getAttribute('data-vaccinationID');

//find the batchNo
const batchNo = new URLSearchParams(window.location.search).get('batchNo');

//#region DOM elements Listeners
document.addEventListener('DOMContentLoaded', async () => {
	await main();
  const admin = await auth(AUTH_RESOURCE.AUTHENTICATE);
  console.log(admin);
  fillUserData(admin);
  await findBatch(batchNo);
  renderBatchInfo(batch);
  vaccinations = await batch.vaccinations; //load batch vaccinations into global variable
  renderBatchTable(getVaccinationsInfo());
})

statusButtonGroup.onchange = _ => {
  const isAccepting = rdbAccept.checked;
  remarksInput.disabled = isAccepting;
  btnSubmit.innerHTML = (isAccepting ? "Confirm" : "Reject") + " Appointment";
}

manageVaccinationForm.onsubmit = (e) => {
  e.preventDefault();
  const vaccination = vaccinations.find(vaccination => vaccination.vaccinationID === getModalVaccinationID());
  const isPending = vaccination.status === 'pending';
  const status = isPending ? manageVaccinationForm['status'].value : "administered";
  const remarks = manageVaccinationForm['remarks'].value;
  
  vaccination.setStatus(status, remarks).then( _ => {
    findBatch(batchNo).then(() => {
  console.table(batch);  renderBatchInfo(batch);

    });
    renderBatchTable(getVaccinationsInfo());
    modalObject.hide();
    showChangesApplied();
  })
}

//truncate the attribute of vaccinations list into id, appointmentDate and status only
function getVaccinationsInfo() {
  return vaccinations.map((vaccination) => ({
    vaccinationID: vaccination.vaccinationID,
    appointmentDate: vaccination.appointmentDate,
    status: vaccination.status,
  }));
}

//load batch into global variable
async function findBatch(batchNo) {
  const data = await request(RESOURCE.BATCH, {query: {'batchNo':batchNo}})
  batch = await data[0];
}

function onVaccinationSelected(vaccinationID) {
  showManageVaccinationModal(vaccinationID);
}

async function showManageVaccinationModal(vaccinationID) {
  const data = await request(RESOURCE.VACCINATION, {
    query: { 'vaccinationID': vaccinationID }
  });

  const vaccination = await data[0];
  updateModalWith(vaccination);
  modalObject.show();
}

//this method fill the infomation inside modal with vaccinations
async function updateModalWith(vaccination) {
  manageVaccinationForm.reset();
  manageVaccinationModal.setAttribute('data-vaccinationID', vaccination.vaccinationID)

  const patientTbody = document.getElementById("patient-info-tbody");
  const rawData = await request(RESOURCE.PATIENT, {query: {'username' : vaccination.patientUID}});
  const patient = await rawData[0];

  const patientInfo = {
    "Full Name" : patient.fullName,
    "IC/Passport" : patient.ICPassport
  }

  fillTbody(patientTbody, patientInfo);

  const batchTbody = document.getElementById("batch-info-tbody");
  
  const batchInfo = {
    "Batch Number": batch.batchNo,
    "Expiry Date": batch.expiryDate,
    "Vaccine Name": (await batch.vaccine).vaccineName,
    "Manufacturer": (await batch.vaccine).manufacturer,
  };

  fillTbody(batchTbody, batchInfo);

  btnSubmit.style.display = 'block';

  switch(vaccination.status) {
    case 'pending': //show approval form
      showStatusGroup();
      btnSubmit.innerHTML = "Update Status";
      operationTitle.innerHTML = "Approval of Appointment"
      break
    case 'confirmed': //show confirmation for administer
      showStatusGroup(false);
      statusButtonGroup.style.display = 'none';
      statusButtonGroup.disabled = 'true';
      btnSubmit.innerHTML = "Confirm Administered"
      operationTitle.innerHTML = "Confirm Vaccination Administered"
      remarksInput.disabled = false;
      break
      case 'rejected':
      case 'administered'://show nonthing but disabled remarks
      showStatusGroup(false);
      statusButtonGroup.style.display = btnSubmit.style.display = 'none';
      remarksInput.value = vaccination.remarks ? vaccination.remarks : "No remarks was recorded.";
      operationTitle.innerHTML = `This vaccination has been ${vaccination.status}.`;
      remarksInput.disabled = true;
      break
  }
}

//#region UI Modifier
function showStatusGroup(show = true) { //show or hide the button group
  statusButtonGroup.style.display = show ? 'inline-flex' : 'none';
  rdbAccept.required = show;
}

function showChangesApplied() { //show the badge and dismiss after 2 seconds
  changesAppliedBadge.classList.remove('d-none')

  setTimeout(() => {
    changesAppliedBadge.classList.add('d-none')
  }, 2000);
}

function renderBatchInfo(batch) {
  batchInfoContainer.innerHTML = '';

  const batchInfo = {
    'Batch Number' : batch.batchNo,
    'Expiry Date' : batch.expiryDate,
    'Quantity Available' : batch.quantityAvailable
  }

  //fill the batch info onto UI
  Object.keys(batchInfo).forEach((key) => {
    const h5 = document.createElement('td');
    h5.innerHTML = key + ' : ' + batchInfo[key];
    batchInfoContainer.appendChild(h5);
  })
}

function renderBatchTable(info) { //this methods render vaccinations table to UI
  renderTable(
    "tableContainer",
    info,
    "vaccinationID",
    ["Vaccination ID", "Appointment Date", "Status"],
    onVaccinationSelected)
}

//#endregion

//#region independent Utilities
function fillTbody(tbody, obj) {
  tbody.innerHTML = "";
  Object.keys(obj).forEach((key) => {
    const tr = document.createElement("tr");
    const labelTd = document.createElement("td");
    labelTd.className = "text-muted";
    labelTd.innerHTML = key;
    const valueTd = document.createElement("td");
    valueTd.className = "text-end";
    valueTd.innerHTML = obj[key];
    tr.appendChild(labelTd);
    tr.appendChild(valueTd);
    tbody.appendChild(tr);
  });
}
//#endregion