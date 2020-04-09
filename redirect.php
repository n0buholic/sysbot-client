<?php
require_once 'vendors/autoload.php';

if ($_GET['query']) {
    $respons = $Sysbot->redirect($_GET['query']);
    $json = $Sysbot->json($respons);

    if ($json->success == false) {
        if (empty($json->redirect)) {
            $Sysbot->showError(404);
        }
        switch ($json->redirect->type) {
        case 'error':
            $Sysbot->showError($json->redirect->code);
            break;
        case 'url':
            header("Location: {$json->redirect->url}");
            break;
        }
    } else {
        header("Location: {$json->link}");
    }
} else {
    $Sysbot->showError(404);
}
