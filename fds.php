<?php

header("Content-Type: application/javascript");

echo file_get_contents("app/lib/serviceWorker.js");
