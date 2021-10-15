import {Modal} from 'bootstrap';
import main from './main';
import {request, METHOD, RESOURCE} from './API'
import { renderTable, appendToTable, createTable } from './module/TableRenderer';
import {Batch} from './model/Batch'

// new Modal(document.getElementById('AddBatchModal')).show();

// const batchesTable = document.getElementById('batches-table');
const vaccineSelect = document.getElementById('vaccine-select');
const centreNameLabel = document.getElementById('healthcare-center-name');
const centreAddressLabel = document.getElementById('healthcare-center-address');
const batchAddedBadge = document.getElementById('batch-added-badge');
const tableContainer = document.getElementById('table-container')
const addBatchForm = document.getElementById('add-batch-form');
const addBatchModal = document.getElementById('add-batch-modal');
const modalObj = new Modal(addBatchModal);

let centre;

document.addEventListener('DOMContentLoaded', async () => {
	await main();

    const username = 'admin_test_1'; // [TODO] find from session later
    const adminResult = await request(RESOURCE.ADMINISTRATOR, {query: {'username' : username}});
    const admin = await adminResult[0];
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
    
    // const formData = new FormData(e.currentTarget);
    // const options = {};

    // for(const [key, value] of formData.entries()){
    //   options[key] = value;
    // }
    const batchNo = addBatchForm['batchNo'].value;
    const expiryDate = addBatchForm['expiryDate'].value;
    const vaccineID = addBatchForm['vaccineID'].value;
    const quantityAvailable = addBatchForm['quantityAvailable'].value;

    const vaccineResult = await request(RESOURCE.VACCINE, { query : {'vaccineID' : vaccineID}});
    const vaccine = await vaccineResult[0];
    
    centre.createBatch(batchNo, expiryDate, vaccine, quantityAvailable)
    .then(res => {
        if(res.message) {
            console.log(res.message);
        }
        else {
            const batch = {'batchNo' : res.batchNo, 'expiryDate' : res.expiryDate, '' : 0}
            appendToTable(batch, 'batchNo', tableContainer.firstChild.childNodes[1])
            modalObj.hide();
            addBatchForm.reset();
            showBatchAdded(batch.batchNo); //res will be the batch object this point
        }
    });

    // console.log(options);
}

function showBatchAdded(batchNo) {
    batchAddedBadge.innerHTML = `Added ${batchNo}`
    batchAddedBadge.classList.remove('d-none')
    setTimeout(() => batchAddedBadge.classList.add('d-none'), 2000);
}