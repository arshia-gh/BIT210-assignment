const vaccineSelect = document.getElementById('vaccineSelect');

vaccineSelect.onchange = (e) => {
    const option = e.target.selectedOptions[0];
    const manufacturer = option.getAttribute('data-manufacturer');
    document.getElementById('manufacturerInput').value = manufacturer;
};

const tableContainer = document.getElementById('tableContainer');
const table = tableContainer.querySelector("table");
table.addEventListener('click', (e) => {
const tr = e.target.parentNode;
const batchNo = tr.getAttribute('data-row-id');
if (batchNo) window.location = '../batch/' + batchNo;
})