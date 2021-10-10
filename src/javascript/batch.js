import { Modal } from "bootstrap";
import { request, METHOD, RESOURCE } from "./API";
import {
  renderTable,
  appendToTable,
  createTable,
} from "./module/TableRenderer";

const manageVaccinationModal = new Modal(document.getElementById("manage-vaccination-modal"));
manageVaccinationModal.show();
// const batchesTable = document.getElementById('batches-table');

(async () => {
  const vaccinations = await request(METHOD.GET, RESOURCE.VACCINATIONS);
  const vaccinationsToRender = vaccinations.map((vaccination) => ({
    vaccinationID: vaccination.vaccinationID,
    appointmentDate: vaccination.appointmentDate,
    status: vaccination.status,
  }));

 
  async function showManageVaccinationModal(vaccinationID) {
const manageVaccinationModal = new Modal(document.getElementById("manage-vaccination-modal"));
manageVaccinationModal.show();

const vaccination = await request(METHOD.GET, RESOURCE.VACCINATIONS, {parameters: {'vaccinationID' : vaccinationID}});
console.log(vaccination[0]);

    // request(METHOD.GET, RESOURCE.VACCINATIONS, {parameters: {'vaccinationID': vaccinationID}})
    // .then(vaccination => {
    //   // showManageVaccinationModal(vaccination);
    // })
  }

  function onVaccinationSelected(vaccinationID) {
    showManageVaccinationModal(vaccinationID)
  }

  const manageVaccinationModal = document.getElementById('manage-vaccination-modal')
//   function updateModalWith(vaccination) {
//     manageVaccinationModal.getElementsByTagName()
//   }

  renderTable(
    "table-container",
    vaccinationsToRender,
    "vaccinationID",
    ["Vaccination ID", "Appointment Date", "Status"],
    onVaccinationSelected
  );
})();

// const manageVaccinationForm = document.getElementById("add-batch-form");

// manageVaccinationForm.onsubmit = (e) => {
//   e.preventDefault();

// };
