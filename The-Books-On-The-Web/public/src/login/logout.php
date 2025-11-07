<?php
session_start();

session_unset();

session_destroy();

header("Location: /ProjetoM2/The-Books-On-The-Web/public/templates/login/entrada.html");
exit;
?>