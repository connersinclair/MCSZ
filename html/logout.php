<?php
unset($_COOKIE['username']);
setcookie('username', '', time() - 3600);
unset($_COOKIE['check']);
setcookie('check', '', time() - 3600);
header("Location: /");