const statusButtonGroup = document.getElementById("statusButtonGroup");
const rdbAccept = document.getElementById("rdbAccept");
const remarksInput = document.getElementById("remarksInput");
const btnSubmit = document.getElementById("submitButton");

//update the text inside submit button depending on the
//toggled status button (either confirm or reject)
if(statusButtonGroup) {
    statusButtonGroup.onchange = () => {
        const isAccepting = rdbAccept.checked;
        remarksInput.disabled = isAccepting;
        btnSubmit.innerHTML = (isAccepting ? "Confirm" : "Reject") + " Appointment";
    };
}
