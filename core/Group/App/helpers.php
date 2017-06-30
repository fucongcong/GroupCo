<?php
if (ASYNC) {
    include(dirname(__FILE__)."/../Async/helpers.php");
} else {
    include("func.php");
}