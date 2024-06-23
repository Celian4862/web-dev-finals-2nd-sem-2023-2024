<?php

/** Print an array in a readable format. */
function printArray(array $arr): void
{
	echo "<ul style='list-style-type: disc; padding: revert' class='list-disc'>";
	foreach ($arr as $key => $val) {
		if (is_array($val)) {
			echo "<li><span>" . $key . "</span><b> => </b><span>";
			printArray($val);
			echo "</span></li>";
		} else {
			echo "<li><span>" . $key . "</span><b> => </b><span>" . $val . "</span></li>";
		}
	}
	echo "</ul>";
}
