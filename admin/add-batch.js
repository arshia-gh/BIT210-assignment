const vaccineSelect = document.getElementById('vaccineSelect');

//show the vaccine manufacturer when user selects a vaccine 
vaccineSelect.onchange = () => {
    const option = vaccineSelect.selectedOptions[0];
    const manufacturer = option.getAttribute('data-manufacturer');
    document.getElementById('manufacturerInput').value = manufacturer;
};

vaccineSelect.onchange();
