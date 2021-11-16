const tableContainer = document.getElementById('tableContainer');
const vaccinationTable = tableContainer.querySelector("table");

vaccinationTable.onclick = (e) => {
  if (e.target.tagName === "TH") return; //do nothing if it is the table header cell

  const tr = e.target.parentNode;
  const vaccinationID = tr.getAttribute("data-row-id");
  if (vaccinationID) window.location = 'manage-vaccination.php?vaccinationID=' + vaccinationID;
}