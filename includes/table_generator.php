<?php
/**
 * generates a html table with the given parameter
 * 
 * @return String html <table>
 * 
 * @param array $objects the array of object whose attribute will be used for generate the table row
 * @param string $objKey the primary key of each table row, which will generate as value of data-row-id attribute
 * @param array $headers the array of header which corresponds to each columns
 * @param bool $renderKey a boolean to specify whether to generate the primary key column or not 
 * @param string $emptyMessage the message to display when there is no objects to generate as table row 
 */
function generate_table(
	array $objects,
	string $objKey,
	array $headers,
	bool $renderKey = true,
	string $emptyMessage = 'There are no data available.'
) {
	echo '
	<table class="table table-white table-hover table-responsive rounded shadow overflow-hidden">
		<thead class="thead table-primary">
			<tr>';
	foreach ($headers as $header) {
		echo "<th>$header</th>";
	}
	echo '	</tr>
		</thead>
		<tbody>';

	if ($objects === null || count($objects) === 0) {
		echo sprintf('<tr><td colspan="%d"> %s </td></tr>', count($headers), $emptyMessage);
	} else {
		foreach ($objects as $obj) {
			echo "<tr role=\"button\" data-row-id=$obj[$objKey]>";
			foreach ($obj as $attributeName => $value) {
				if ($attributeName === $objKey && $renderKey === false)
					continue;
				echo "<td>$value</td>";
			}
			echo '</tr>';
		}
	}
	echo '
		</tbody>
	</table>';
}
