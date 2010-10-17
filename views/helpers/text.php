<?php
class TextHelper {

	function truncate($desc, $size = 25) {
		$trunc = false;
		$short = "";
		$d = explode(" ", $desc);
		foreach ($d as $str) {
			if (strlen($short) < $size) {
				$short .= $str . " ";
			} else {
				$trunc = true;
				break;
			}
		}

		if ($trunc)
		$short .= "...";

		return $short;
	}

}