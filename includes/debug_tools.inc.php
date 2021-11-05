<?php
	function print_table(array $array): void
	{
		echo "<table class='table table-hover table-light'>";
		foreach ($array as $index => $obj) {

			if ($index === 0) {
				echo "<tr class='table-secondary'>";
				foreach ($obj as $key => $value) {
					echo "<th>$key</th>";
				}
				echo "</tr>";
			}

			echo "<tr>";
			foreach ($obj as $key => $value) {
				echo "<td>$value<br /></td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}

	function pretty_print(array $array): void {
		echo '<pre>';
		print_r($array);
		echo '<pre>';

	}