const tableContainer = document.getElementById('tableContainer');
const batchTable = tableContainer.querySelector("table");
batchTable.addEventListener('click', (e) => {
const tr = e.target.parentNode;
const batchNo = tr.getAttribute('data-row-id');
if (batchNo) window.location = 'batch.php?batchNo=' + batchNo;
})