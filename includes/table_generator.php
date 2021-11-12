<?php

// 	containerID,
// 	objects,
// 	objKey,
// 	headers,
// 	onRowClick,
// 	renderKey = true
// ) => {
// 	const container = document.getElementById(containerID);

// 	if (!container) throw new ReferenceError('Invalid container id; Element not found.');

// 	const table = createTable(objects, objKey, headers, onRowClick, renderKey);

// 	container.replaceChildren(table);
// };

function GenerateTable(array $objects, string $objKey, array $headers, bool $renderKey = true, string $emptyMessage = null) {
	echo '
	<table class="table rounded table-white overflow-hidden table-hover table-responsive shadow" id="lmao">
		<thead class="thead table-primary">
			<tr>';
			foreach($headers as $header) {
				echo "<th>$header</th>";
			}
	echo '	</tr>
		</thead>
		<tbody>';

		if($objects === null || count($objects) === 0) {
			echo sprintf('<tr><td colspan="%d"> %s </td></tr>', count($headers),
				$emptyMessage ?? 'There are no data available.'); 
		}
		else {
			foreach($objects as $obj) {
				echo "<tr role=\"button\" data-row-id=$obj[$objKey]>";
				foreach($obj as $attributeName => $value) {
					if($attributeName === $objKey && $renderKey === false) 
						continue;
					echo "<td>$value</td>";
				}
				echo '</>';
			}
		}
	echo '
		</tbody>
	</table>';

	// echo "
	// <script>
	// 	const table = document.getElementById('lmao');

	// 	table.addEventListener('click', (e) => {
	// 	let tr = e.target.parentNode;
	// 	let rowID = tr.getAttribute('data-$objKey');
	// 	if (rowID) alert(rowID);
	// 	})
	// </script>";

}

// const createTable =  => {
// 	const table = document.createElement('table');
// 	table.className =
// 		'table rounded table-white overflow-hidden table-hover table-responsive shadow'; //not using add() because there's no previous class retained

// 	const thead = document.createElement('thead');
// 	thead.className = 'table-primary';
// 	table.appendChild(thead);

// 	const tbody = document.createElement('tbody');
// 	table.appendChild(tbody);

// 	const headerRow = document.createElement('tr');

// 	for (const header of headers) {
// 		if ((!renderKey && header !== objKey) || renderKey) {
// 			const th = document.createElement('th');
// 			th.innerHTML = header;
// 			headerRow.appendChild(th);
// 		}
// 	}

// 	table.appendChild(thead);
// 	thead.appendChild(headerRow);
// 	table.appendChild(tbody);

// 	if (objects.length)
// 		//if theres any data
// 		objects.forEach((obj) => appendToTable(obj, objKey, tbody, renderKey));
// 	//this insert rows to table
// 	else {
// 		appendToTable({ prop: 'No data recorded' }, null, tbody);
// 		table.classList.remove('table-hover');
// 	}

// 	table.addEventListener('click', (e) => {
// 		let tr = e.target.parentNode;
// 		let rowID = tr.getAttribute('data-' + objKey);
// 		if (rowID) {
// 			onRowClick(rowID);
// 		}
// 	});

// 	return table;
// };

// export const appendToTable = (obj, key, table, renderKey = true) => {
// 	const tr = document.createElement('tr');
// 	tr.setAttribute('role', 'button');
// 	tr.setAttribute('data-' + key, obj[key]);

// 	for (let prop in obj) {
// 		if (obj.hasOwnProperty(prop) && ((!renderKey && prop !== key) || renderKey)) {
// 			let td = document.createElement('td');
// 			td.innerHTML = obj[prop];
// 			tr.appendChild(td);
// 		}
// 	}

// 	table.appendChild(tr);
// };
?>