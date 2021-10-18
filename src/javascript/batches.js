import {Modal} from 'bootstrap';
import main, {fillUserData} from './main';
import {request, RESOURCE} from './API'
import { renderTable, appendToTable } from './module/TableRenderer';

const vaccineSelect = document.getElementById('vaccine-select');
const centreNameLabel = document.getElementById('healthcare-center-name');
const centreAddressLabel = document.getElementById('healthcare-center-address');
const batchAddedBadge = document.getElementById('batch-added-badge');
const tableContainer = document.getElementById('table-container')
const addBatchForm = document.getElementById('add-batch-form');
const duplicateAlert = document.getElementById('duplicate-batch-alert');
const addBatchModal = document.getElementById('add-batch-modal');
const modalObj = new Modal(addBatchModal);

addBatchModal.addEventListener('show.bs.modal', () => {
    duplicateAlert.classList.add('d-none');
    addBatchForm.reset();
})

let centre;

document.addEventListener('DOMContentLoaded', async () => {
	await main();

    const username = 'admin_test_1'; // [TODO] find from session later
    const adminResult = await request(RESOURCE.ADMINISTRATOR, {query: {'username' : username}});
    const admin = await adminResult[0];
    fillUserData(admin);
    centre = await admin.healthcareCenter;
    centreNameLabel.innerHTML = centre.centerName;
    centreAddressLabel.innerHTML = centre.address;

    //#region vaccine select initialisation
    const vaccines = await request(RESOURCE.VACCINE);

    vaccines.forEach(vaccine => {
        const option = document.createElement('option');
        option.value = vaccine.vaccineID;
        option.innerHTML = vaccine.vaccineName;
        option.setAttribute('data-manufacturer', vaccine.manufacturer);
        vaccineSelect.appendChild(option);
    });
    //#endregion

    const batches = await centre.batches;
    const batchesToRender = await Promise.all(batches.map(async batch =>
        (
            {'batchNo': batch.batchNo,
            'expiryDate': batch.expiryDate,
            "noOfPendingAppointment": (await batch.vaccinations)
            .filter(vaccination => vaccination.status === 'Pending').length})
        )
     )

    function onBatchSelected(batchNo) {
        location = location.origin + '/batch.html?batchNo=' + batchNo;
    }

    renderTable('table-container', batchesToRender, 'batchNo',
    ['Batch Number', 'Expiry Date', 'No of Pending Appointment'],
    onBatchSelected);
});


vaccineSelect.onchange = e => {
    const option = e.target.selectedOptions[0];
    const manufacturer = option.getAttribute('data-manufacturer');
    document.getElementById('manufacturerInput').value = manufacturer;
}

addBatchForm.onsubmit = async e => {
    e.preventDefault();
    
    const batchNo = addBatchForm['batchNo'].value;
    const expiryDate = addBatchForm['expiryDate'].value;
    const vaccineID = addBatchForm['vaccineID'].value;
    const quantityAvailable = addBatchForm['quantityAvailable'].value;

    const vaccineResult = await request(RESOURCE.VACCINE, { query : {'vaccineID' : vaccineID}});
    const vaccine = await vaccineResult[0];
    
    centre.createBatch(batchNo, expiryDate, vaccine, quantityAvailable)
    .then(res => { //res will be the batch object this point
        const batch = {'batchNo' : res.batchNo, 'expiryDate' : res.expiryDate, '' : 0}
        appendToTable(batch, 'batchNo', tableContainer.firstChild.childNodes[1]) //refers to tbody
        modalObj.hide();
        addBatchForm.reset();
        showBatchAdded(batch.batchNo); 
    })
    .catch(() => {
        duplicateAlert.innerHTML = `Batch number ${batchNo} already exists for ${vaccine.vaccineName}` 
        duplicateAlert.classList.remove('d-none');
    })
}

function showBatchAdded(batchNo) {
    batchAddedBadge.innerHTML = `Added ${batchNo}`
    batchAddedBadge.classList.remove('d-none')
    setTimeout(() => batchAddedBadge.classList.add('d-none'), 2000);
}