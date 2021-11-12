const statusButtonGroup = document.getElementById("statusButtonGroup");
const rdbAccept = document.getElementById("rdbAccept");
const remarksInput = document.getElementById("remarksInput");
const btnSubmit = document.getElementById("submitButton");

if(statusButtonGroup) {
    statusButtonGroup.onchange = () => {
        const isAccepting = rdbAccept.checked;
        remarksInput.disabled = isAccepting;
        btnSubmit.innerHTML = (isAccepting ? "Confirm" : "Reject") + " Appointment";
    };
}
