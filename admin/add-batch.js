const vaccineSelect = document.getElementById('vaccineSelect');

vaccineSelect.onchange = () => {
    const option = vaccineSelect.selectedOptions[0];
    const manufacturer = option.getAttribute('data-manufacturer');
    document.getElementById('manufacturerInput').value = manufacturer;
};

vaccineSelect.onchange();
