<?php
session_start();

require_once('include/twig.php');
require_once('include/fonctions.php');
require_once('include/connexion.php');
require_once('include/theme.php');
require_once('include/article.php');
require_once('include/element.php');
require_once('include/cours.php');

// Initialisation de Twig
$twig = init_twig();

// Route (page/action/id) pour le contrôleur
if (isset($_GET['page'])) $page = $_GET['page'];
else $page = '';
if (isset($_GET['action'])) $action = $_GET['action'];
else $action = 'read';
if (isset($_GET['id'])) $id = intval($_GET['id']);
else $id = 0;

// Vérification des droits administrateur
// Charge le login stocké dans la session

// Le tableau de données par défaut
$view = '';
$data = [];

switch ($page) {
    case 'theme':
        Theme::controleurPro($action, $id, $view, $data);
        break;
    case 'article':
        Article::controleurPro($action, $id, $view, $data);
        break;
    case 'element':
        Element::controleurPro($action, $id, $view, $data);
        break;
    case 'cours': 
        Cours::controleurPro($action, $id, $view, $data);
        break;
    case 'logout':
        unset($_SESSION['login']);
        header('Location: visiteur.php');
        break;
    default:
        $view = 'pro.twig';
        $data = [];
}

// Ajoute les informations de login
echo $twig->render($view, $data);