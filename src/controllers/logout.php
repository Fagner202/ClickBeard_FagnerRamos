<?php
// Invalida o cookie do token
setcookie('token', '', time() - 3600, '/', '', false, true);

// Redireciona para a página de login
header('Location: /login');
exit;