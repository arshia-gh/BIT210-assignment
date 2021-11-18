const tableContainer = document.getElementById("tableContainer");
const batchTable = tableContainer.querySelector("table");

batchTable.onclick = (e) => {
  if (e.target.tagName === "TH") return; //do nothing if it is the table header cell
  
  const tr = e.target.parentNode;
  const batchNo = tr.getAttribute("data-row-id");

  if (batchNo) 
    window.location = "batch.php?batchNo=" + batchNo;
};
