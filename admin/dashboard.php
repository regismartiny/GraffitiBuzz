<?php
if (isset($_POST['user']) && isset($_POST['pass'])) {
	if ($_POST['user'] == 'admin' && $_POST['pass'] == 'senhaadmin') {
		echo "Bem vindo ao painel administrativo.";
	} else {
		echo "Acesso negado.";
	}
}
?>