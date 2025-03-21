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

// Le tableau de données par défaut
$data = [];

switch ($page) {
    case 'theme':
        Theme::controleur($action, $id, $view, $data);
        break;

    case 'cours':  
        switch ($action) {
            case 'read':
                $view = 'detail.twig';
                $cours = Cours::readOne($id); 
                $data = [
                    'cours' => $cours
                ];
                break;

            case 'create':
                $cours = new Cours();
                $messages = [];
                if (isset($_POST['form_cours'])) {
                    $ok = $cours->lirePost($messages);
                    if ($ok) {
                        $cours->create();
                        header('Location: ?page=cours&id=' . $cours->id);
                        exit;
                    }
                }
                $view = 'form.twig';
                $data = [
                    'cours' => $cours,
                    'messages' => $messages
                ];
                break;

            case 'update':
                $cours = Cours::readOne($id);
                $messages = [];
                if (isset($_POST['form_cours'])) {
                    $ok = $cours->lirePost($messages);
                    if ($ok) {
                        $cours->update();
                        header('Location: ?page=cours&id=' . $cours->id);
                        exit;
                    }
                }
                $view = 'form.twig';
                $data = [
                    'cours' => $cours,
                    'messages' => $messages
                ];
                break;

            case 'search':
                $prix_min = $_GET['prix_min'] ?? null;
                $prix_max = $_GET['prix_max'] ?? null;
                $date = $_GET['date'] ?? null;
                $liste_cours = Cours::search($prix_min, $prix_max, $date);
                $view = 'cours.twig';
                $data = ['liste_cours' => $liste_cours];
                break;

            case 'delete':
                $cours = Cours::readOne($id);
                $cours->delete();
                header('Location: ?page=cours');
                exit;
                break;
        }
        break;

    case 'article':
        switch ($action) {
            case 'read':
                $view = 'detail_article.twig';
                $article = Article::readOne($id);
                $elements = Element::readByArticle($id);
                $data = [
                    'article' => $article,
                    'elements' => $elements
                ];
                break;

            case 'create':
                $article = new Article();
                $messages = [];
                if (isset($_POST['form_article'])) {
                    $ok = $article->lirePost($messages);
                    if ($ok) {
                        $article->create();
                        header('Location: ?page=article&id=' . $article->id);
                        exit;
                    }
                }
                $view = 'form_article.twig';
                $theme = Theme::readOne($id);
                $data = [
                    'theme' => $theme,
                    'article' => $article,
                    'elements' => [],
                    'messages' => $messages
                ];
                break;

            case 'update':
                $article = Article::readOne($id);
                $messages = [];
                if (isset($_POST['form_article'])) {
                    $ok = $article->lirePost($messages);
                    if ($ok) {
                        $article->update();
                        header('Location: ?page=article&id=' . $article->id);
                        exit;
                    }
                }
                $elements = Element::readByArticle($id);
                $view = 'form_article.twig';
                $data = [
                    'article' => $article,
                    'elements' => $elements,
                    'messages' => $messages
                ];
                break;

            case 'delete':
                $article = Article::readOne($id);
                $article->delete();
                header('Location: ?page=theme&id=' . $article->id_theme);
                exit;
                break;
        }
        break;

    case 'element':
        switch ($action) {
            case 'create':
                $element = new Element();
                $messages = [];
                if (isset($_POST['form_element'])) {
                    $ok = $element->chargePOST($messages);
                    if ($ok) {
                        $element->create();
                        header('Location: ?page=article&action=update&id=' . $id);
                        exit;
                    }
                }
                $view = 'ajouter_elements.twig';
                $article = Article::readOne($id);
                $videos = listVideo();
                $data = [
                    'article' => $article,
                    'element' => $element,
                    'videos' => $videos,
                    'messages' => $messages
                ];
                break;

            case 'update':
                $element = Element::readOne($id);
                $messages = [];
                if (isset($_POST['form_element'])) {
                    $ok = $element->chargePOST($messages);
                    if ($ok) {
                        $element->update();
                        header('Location: ?page=article&action=update&id=' . $element->id_article);
                        exit;
                    }
                }
                $view = 'ajouter_elements.twig';
                $article = Article::readOne($element->id_article);
                $videos = listVideo();
                $data = [
                    'article' => $article,
                    'element' => $element,
                    'videos' => $videos,
                    'messages' => $messages
                ];
                break;

            case 'delete':
                $element = Element::readOne($id);
                $id_article = $element->id_article;
                $element->delete();
                header('Location: ?page=article&action=update&id=' . $id_article);
                exit;
                break;
        }
        break;

    case 'login':
        $view = 'login.twig';
        break;
    
    
        case 'about':
            $view = 'about.twig'; // Indique que cette page utilise le template about.twig
            $data = [
                'title' => 'À propos de nous',
                'content' => 'Bienvenue sur notre site. Voici la page À propos, où vous trouverez des informations sur nous.'
            ]; // Ajoute les données nécessaires pour Twig
            break;

            case 'contact':
                $view = 'contact.twig'; // Appelle le fichier Twig
                $data = [
                    'title' => 'Contactez-nous',
                    'description' => 'Prenez contact avec notre équipe pour toute demande ou projet.',
                    'email' => 'contact@monacocouture.com',
                    'phone' => '+33 6 12 34 56 78',
                    'address' => '10 Rue de la Paix, 75000 Paris',
                ];
                break;
            
            case 'contact_submit':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Gestion du formulaire
                    $name = htmlspecialchars($_POST['name']);
                    $email = htmlspecialchars($_POST['email']);
                    $subject = htmlspecialchars($_POST['subject']);
                    $message = htmlspecialchars($_POST['message']);
            
                    // Par exemple : Envoi d'email ou traitement
                    mail(
                        'contact@monacocouture.com',
                        "Message de $name - $subject",
                        $message,
                        "From: $email"
                    );
            
                    // Redirection ou message de confirmation
                    header('Location: visiteur.php?page=contact&status=success');
                    exit;
                }
                break;
        case 'valid_login':
            // Teste si le formulaire login a été rempli
            if (isset($_POST['login'])) {
                $login = postString('login');
                // Si le mot de passe est correct
                if ($login == "admin301") {
                    // Enregistre le login dans la session et charge le controleur admin
                    $_SESSION['login'] = $login;
                    header('Location: admin.php');
                } else {
                    // Mot de passe incorrect : retour à la page login
                    echo "<p> Le mot de passe est incorrect </p>";
                    unset($_SESSION['login']);
                    header('Location: visiteur.php?login');
                }
                //header('Location: visiteur.php?login');
            } else {
    
                // Accès hors formulaire : retour à la page d'accueil
                header('Location: visiteur.php');
            }
            break;
            // LOGIN POUR PRO
            case 'login_pro':
                $view = 'login_pro.twig';
                break;
            case 'valid_login_pro':
                // Teste si le formulaire login a été rempli
                if (isset($_POST['login_pro'])) {
                    $login = postString('login_pro');
                    // Si le mot de passe est correct
                    if ($login == "pro301") {
                        // Enregistre le login dans la session et charge le controleur pro
                        $_SESSION['login_pro'] = $login;
                        header('Location: professionnel.php');
                    } else {
                        // Mot de passe incorrect : retour à la page login
                        echo "<p> Le mot de passe est incorrect </p>";
                        unset($_SESSION['login_pro']);
                        header('Location: visiteur.php?login_pro');
                    }
                    //header('Location: visiteur.php?login');
                } else {
        
                    // Accès hors formulaire : retour à la page d'accueil
                    header('Location: visiteur.php');
                }
                break;
        default:
            Theme::controleur($action, $id, $view, $data);
    }
    
    // Ajoute les informations de login
    echo $twig->render($view, $data);
    