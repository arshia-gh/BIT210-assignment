<?php

function GenerateTable(
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
