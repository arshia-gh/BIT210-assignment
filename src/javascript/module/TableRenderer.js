/**
 * 
 * @param {string} containerID - the id of the container of this table
 * @param {array} objects - the list of object to be created as table row
 * @param {string} headers - the column headers
 * @param {string} objKey - the key or id used to identify the table row when creating 
 * @param {function} onRowClick - the callback function to invoke when a row is clicked
 */
export const renderTable = (containerID, objects, objKey, headers, onRowClick) => {
    
    const container = document.getElementById(containerID);

    if(!container) throw new ReferenceError('Invalid container id; Element not found.')

    const table = createTable(objects, objKey, headers, onRowClick);

    container.replaceChildren(table);
}

export const createTable = (objects, objKey, headers, onRowClick) => {
    const table = document.createElement("table");
    //[TODO] make table scrollable with fixed header
    table.className = "table rounded table-white overflow-hidden table-hover table-responsive shadow"; //not using add() because there's no previous class retained

    const thead = document.createElement("thead");
    thead.className = "table-primary";
    table.appendChild(thead);

    const tbody = document.createElement("tbody");
    table.appendChild(tbody);

    const headerRow = document.createElement("tr");

    for(const header of headers) {
        const th = document.createElement('th');
        th.innerHTML = header;
        headerRow.appendChild(th);
    }

    table.appendChild(thead);
    thead.appendChild(headerRow);
    table.appendChild(tbody);

    if(objects.length) //if theres any data
        objects.forEach(obj => appendToTable(obj, objKey, tbody)); //this insert rows to table
    else {
        appendToTable({'prop' : 'No data recorded'}, null, tbody);
        table.classList.remove('table-hover');
    }

    table.addEventListener('click', (e) => {
        let tr = e.target.parentNode;
        let rowID = tr.getAttribute('data-' + objKey);
        if (rowID) {
            onRowClick(rowID)
        }
    })

    return table;
}

export const appendToTable = (obj, key, table) => {
    const tr = document.createElement("tr");
    tr.setAttribute('role', 'button');
    tr.setAttribute('data-' + key, obj[key]);

    for (let prop in obj) {
        let td = document.createElement('td');
        td.innerHTML = obj[prop];
        tr.appendChild(td);
    }

    table.appendChild(tr);
};


