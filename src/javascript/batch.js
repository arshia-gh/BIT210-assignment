import { Modal } from "bootstrap";
import { request, METHOD, RESOURCE } from "./API";
import {
  renderTable,
  appendToTable,
  createTable,
} from "./module/TableRenderer";

const batchInfoContainer = document.getElementById('batch-info-container');

const vaccinationApprovalControls = document.getElementById("accept-reject-controls");
const vaccinationConfirmationControls = document.getElementById("confirm-vaccination-controls");

const vaccinationApprovalForm = document.getElementById('accept-reject-vaccination-form');
const vaccinationConfirmationForm = document.getElementById("confirm-vaccination-form");

const manageVaccinationModal = document.getElementById("manage-vaccination-modal");
const modalObject = new Modal(manageVaccinationModal);

(async () => {
  const params = new URLSearchParams(window.location.search)
  const batchNo = params.get('batchNo');
  const data = await request(METHOD.GET, RESOURCE.BATCHES, {parameters: {'batchNo':batchNo}})
  const batch = await data[0];

  const batchInfo = {
    'Batch Number' : batch.batchNo,
    'Expiry Date' : batch.expiryDate,
    'Quantity Available' : batch.quantityAvailable - batch.quantityAdministered
  }

  Object.keys(batchInfo).forEach((key) => {
    const h5 = document.createElement('td');
    h5.innerHTML = key + ' : ' + batchInfo[key];
    batchInfoContainer.appendChild(h5);
  })

  const vaccinations = await request(METHOD.GET, RESOURCE.VACCINATIONS);
  const vaccinationsToRender = vaccinations.map((vaccination) => ({
    vaccinationID: vaccination.vaccinationID,
    appointmentDate: vaccination.appointmentDate,
    status: vaccination.status,
  }));

  renderTable(
    "table-container",
    vaccinationsToRender,
    "vaccinationID",
    ["Vaccination ID", "Appointment Date", "Status"],
    onVaccinationSelected
  );
})();

const getModalVaccinationID = _ => manageVaccinationModal.getAttribute('data-vaccinationID');

async function showManageVaccinationModal(vaccinationID) {
  modalObject.show();

  const data = await request(METHOD.GET, RESOURCE.VACCINATIONS, {
    parameters: { vaccinationID: vaccinationID },
  });

  const vaccination = await data[0];

  updateModalWith(vaccination);
  // request(METHOD.GET, RESOURCE.VACCINATIONS, {parameters: {'vaccinationID': vaccinationID}})
  // .then(vaccination => {
  //   // showManageVaccinationModal(vaccination);
  // })
}

function onVaccinationSelected(vaccinationID) {
  showManageVaccinationModal(vaccinationID);
}

function updateModalWith(vaccination) {
  const patientTbody = document.getElementById("patient-info-tbody");
  // const patient = await request(METHOD.GET, RESOURCE.PATIENTS, {parameters: {'username' : vaccinationID}})[0];
  const batchTbody = document.getElementById("batch-info-tbody");
  // const batch = await request(METHOD.GET, RESOURCE.BATCHES, {parameters: {'batchNo' : vaccination.batchNo}})[0];
  const batchInfo = {
    "Batch Number": vaccination.batch.batchNo,
    "Expiry Date": vaccination.batch.expiryDate,
    "Vaccine Name": vaccination.batch.vaccine.vaccineName,
    Manufacturer: vaccination.batch.vaccine.manufacturer,
  };

  fillTbody(batchTbody, batchInfo);

  const controlPlaceholder = document.getElementById("controls-placeholder");
  let isPending = vaccination.status === 'Pending';
  // const controlID = status === 'Pending' ? 'accept-reject' : 'confirm-administered';
  vaccinationApprovalControls.style.display = isPending ? 'block' : 'none';//vaccinationApprovalControls : vaccinationConfirmationControls;
  vaccinationConfirmationControls.style.display = isPending ? 'none' : 'block';
  vaccinationApprovalForm.reset();
  vaccinationConfirmationForm.reset();
  // const targetControl = isPending ? vaccinationApprovalControls : vaccinationConfirmationControls;
  manageVaccinationModal.setAttribute('data-vaccinationID', vaccination.vaccinationID)
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

// manageVaccinationModal.show();
// const batchesTable = document.getElementById('batches-table');

vaccinationApprovalForm.onsubmit = (e) => {
  e.preventDefault();
  alert(getModalVaccinationID());
}

vaccinationConfirmationForm.onsubmit = (e) => {
  e.preventDefault();
  alert(getModalVaccinationID());
}




// manageVaccinationForm.onsubmit = (e) => {
//   e.preventDefault();

// };
