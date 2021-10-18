import main from './main';
import { Modal } from "bootstrap";
import { request, METHOD, RESOURCE } from "./API";
import {
  renderTable,
  appendToTable,
  createTable,
} from "./module/TableRenderer";

const batchInfoContainer = document.getElementById('batch-info-container');

const manageVaccinationForm = document.getElementById('manage-vaccination-form');
const manageVaccinationModal = document.getElementById("manage-vaccination-modal");
const modalObject = new Modal(manageVaccinationModal);

const statusButtonGroup = document.getElementById('status-button-group');
const rdbAccept = document.getElementById('rdbtn-accept');
const remarksInput = document.getElementById('remarks-input');
const btnSubmit = document.getElementById('submit-button');
const operationTitle = document.getElementById('operation-title');

const changesAppliedBadge = document.getElementById('changes-applied');

const getModalVaccinationID = _ => manageVaccinationModal.getAttribute('data-vaccinationID');

statusButtonGroup.onchange = _ => {
  const isAccepting = rdbAccept.checked;
  remarksInput.disabled = isAccepting;
  btnSubmit.innerHTML = (isAccepting ? "Confirm" : "Reject") + " Appointment";
}

let batch, vaccinations;

document.addEventListener('DOMContentLoaded', async () => {
	await main();


  //find the batchNo
  const params = new URLSearchParams(window.location.search)
  const batchNo = params.get('batchNo');
  const data = await request(RESOURCE.BATCH, {query: {'batchNo':batchNo}})
  batch = await data[0];

  const batchInfo = {
    'Batch Number' : batch.batchNo,
    'Expiry Date' : batch.expiryDate,
    'Quantity Available' : batch.quantityAvailable - batch.quantityAdministered
  }

  //fill the batch info onto UI
  Object.keys(batchInfo).forEach((key) => {
    const h5 = document.createElement('td');
    h5.innerHTML = key + ' : ' + batchInfo[key];
    batchInfoContainer.appendChild(h5);
  })

  vaccinations = await batch.vaccinations;

  renderBatchTable(getVaccinationToRender());
})

/**
 * gets the global vaccinations and select required attribute from it
 * @returns a array of vaccination objects with id, appointmentDate and status only
 */
function getVaccinationToRender() {
  return vaccinations.map((vaccination) => ({
    vaccinationID: vaccination.vaccinationID,
    appointmentDate: vaccination.appointmentDate,
    status: vaccination.status,
  }));
}

//render vaccinations table to UI
function renderBatchTable(info) {
  renderTable(
    "table-container",
    info,
    "vaccinationID",
    ["Vaccination ID", "Appointment Date", "Status"],
    onVaccinationSelected)
}

async function showManageVaccinationModal(vaccinationID) {

  const data = await request(RESOURCE.VACCINATION, {
    query: { 'vaccinationID': vaccinationID }
  });

  const vaccination = await data[0];
  updateModalWith(vaccination);
  modalObject.show();
}

function onVaccinationSelected(vaccinationID) {
  showManageVaccinationModal(vaccinationID);
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

function showStatusGroup(show = true) {
  statusButtonGroup.style.display = show ? 'inline-flex' : 'none';
  rdbAccept.required = show;
}

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

manageVaccinationForm.onsubmit = (e) => {
  e.preventDefault();
  const vaccination = vaccinations.find(vaccination => vaccination.vaccinationID === getModalVaccinationID());
  const isPending = vaccination.status === 'pending';
  const status = isPending ? manageVaccinationForm['status'].value : "administered";
  const remarks = manageVaccinationForm['remarks'].value;
  
  vaccination.setStatus(status, remarks).then( _ => {
    renderBatchTable(getVaccinationToRender());
    modalObject.hide();
    showChangesApplied();
  })
}

function showChangesApplied() {
  changesAppliedBadge.classList.remove('d-none')

  setTimeout(() => {
    changesAppliedBadge.classList.add('d-none')
  }, 2000);
}
