import {Modal} from 'bootstrap';
import main, {fillUserData} from './main';
import {auth, AUTH_RESOURCE, request, RESOURCE} from './API'
import { renderTable, appendToTable } from './module/TableRenderer';

const vaccineSelect = document.getElementById('vaccineSelect');
const centreNameLabel = document.getElementById('healthcareCenterName');
const centreAddressLabel = document.getElementById('healthcareCenterAddress');
const batchAddedBadge = document.getElementById('batchAddedBadge');
const tableContainer = document.getElementById('tableContainer')
const addBatchForm = document.getElementById('addBatchForm');
const expiryDateInput = document.getElementById('expiryDateInput');
const duplicatedAlert = document.getElementById('duplicatedBatchAlert');
const addBatchModal = document.getElementById('addBatchModal');
const modalObj = new Modal(addBatchModal);

const todayDate = new Date().toISOString().split('T')[0];
expiryDateInput.setAttribute('min', todayDate);

addBatchModal.addEventListener('show.bs.modal', () => {
    duplicatedAlert.classList.add('d-none');
    addBatchForm.reset();
})

let centre;

document.addEventListener('DOMContentLoaded', async () => {
	await main();

    const admin = await auth(AUTH_RESOURCE.AUTHENTICATE);
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
            .filter(vaccination => vaccination.status === 'pending').length})
        )
     )

    function onBatchSelected(batchNo) {
        location = location.origin + '/batch.html?batchNo=' + batchNo;
    }

    //render the table to UI
    renderTable('tableContainer', batchesToRender, 'batchNo',
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
        const batch = {'batchNo' : res.batchNo, 'expiryDate' : res.expiryDate, 'noOfPendingAppointment' : 0}
        appendToTable(batch, 'batchNo', tableContainer.firstChild.childNodes[1]) //refers to tbody
        modalObj.hide();
        addBatchForm.reset();
        showBatchAdded(batch.batchNo); 
    })
    .catch(() => {
        duplicatedAlert.innerHTML = `Batch number ${batchNo} already exists for ${vaccine.vaccineName}` 
        duplicatedAlert.classList.remove('d-none');
    })
}

function showBatchAdded(batchNo) {
    batchAddedBadge.innerHTML = `Added ${batchNo}`
    batchAddedBadge.classList.remove('d-none')
    setTimeout(() => batchAddedBadge.classList.add('d-none'), 2000);
}