<?php
if (ASYNC) {
	include("func.php");
} else {
    include(dirname(__FILE__)."/../Sync/helpers.php");
}