import {Modal} from 'bootstrap';
import {request, METHOD, RESOURCE} from './API'
import { renderTable, appendToTable, createTable } from './module/TableRenderer';
import {Batch} from './model/Batch'

// new Modal(document.getElementById('AddBatchModal')).show();

// const batchesTable = document.getElementById('batches-table');
const vaccineSelect = document.getElementById('vaccine-select');

vaccineSelect.onchange = e => {
    const option = e.target.selectedOptions[0];
    const manufacturer = option.getAttribute('data-manufacturer');
    document.getElementById('manufacturerInput').value = manufacturer;
}

(async () => {
    const vaccines = await request(METHOD.GET, RESOURCE.VACCINES);

    vaccines.forEach(vaccine => {
        const option = document.createElement('option');
        option.value = vaccine.vaccineID;
        option.innerHTML = vaccine.vaccineName;
        option.setAttribute('data-manufacturer', vaccine.manufacturer);
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

const addBatchForm = document.getElementById('add-batch-form');

addBatchForm.onsubmit = e => {
    e.preventDefault();
    
    const formData = new FormData(e.currentTarget);
    const options = {};

    for(const [key, value] of formData.entries()){
      options[key] = value;
    }

    const batch = new Batch(options)

    request(METHOD.POST, RESOURCE.BATCHES, {content:batch})
    .then(res => {
        if(res.message) {
            alert('got error');
        }
        else {

        }
    });

    // console.log(options);
    
}
