import {Modal} from 'bootstrap';
import {request, METHOD, RESOURCE} from './API'
import { renderTable, appendToTable, createTable } from './module/TableRenderer';

// new Modal(document.getElementById('AddBatchModal')).show();

// const batchesTable = document.getElementById('batches-table');
const vaccineSelect = document.getElementById('vaccine-select');

(async () => {
    const vaccines = await request(METHOD.GET, RESOURCE.VACCINES);

    vaccines.forEach(vaccine => {
        const option = document.createElement('option');
        option.value = vaccine.vaccineID;
        option.innerHTML = vaccine.vaccineName;
        vaccineSelect.appendChild(option);
    });

    const batches = await request(METHOD.GET, RESOURCE.BATCHES);
    const batchesToRender = batches.map(batch => 
        (
            {'batchNo': batch.batchNo, 
            'expiryDate': batch.expiryDate,
            "noOfPendingAppointment": batch.vaccinations.filter(vaccination => vaccination.status === 'Pending').length})
        )
        

    function onBatchSelected(batchNo) {
        location = location.origin + '/batch.html?batchNo=' + batchNo;
    }

    renderTable('table-container', batchesToRender, 'batchNo',
    ['Batch Number', 'Expiry Date', 'No of Pending Appointment'],
    onBatchSelected);


})();

// batchesTable.addEventListener('click', (e) => {
//     let tr = e.target.parentNode;
//     let batchNo = tr.getAttribute('data-batchNo');
//     if (batchNo) {
//         location = location.origin + '/batch.html?batchNo=' + batchNo;
//         // let selectedBatch = vaccines.find(batch => batch.batchNo === batchNo);
//     }
// })
