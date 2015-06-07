<?php
$ref = $_GET['ref'];
echo $ref;
if (isset($_GET['ref'])) {
    $refCheck = $ref.".php";
    if (file_exists($refCheck)) {
        header("Location: $ref");
    } else {
        header("Location: /");
    }
} else {
    header("Location: /");
}

//Silly person that decided to open this file, close it as it has nothing you should change
//Unless of course you're adding multi-directory support in which I am too lazy to do for now because it's not needed