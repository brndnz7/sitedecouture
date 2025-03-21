<?php

require_once('fonctions.php');
require_once('cours.php'); 
class Theme
{
	public $id;
	public $nom;
	public $description;
	public $image;

	// Le constructeur corrige les données récupérées de la BDD
	// Ici convertie l'identifiant en entier
	function __construct()
	{
		$this->id = intval($this->id);
	}

	static function readAll()
	{
		$sql = 'SELECT * FROM theme';
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_CLASS, 'Theme');
	}

	static function readOne($id)
	{
		$sql = 'SELECT * FROM theme WHERE id = :id';
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
		return $query->fetchObject('Theme');
	}

	function create()
	{
		$sql = "INSERT INTO theme (nom,description,image) VALUES (:nom, :description, :image)";
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':nom', $this->nom, PDO::PARAM_STR);
		$query->bindValue(':description', $this->description, PDO::PARAM_STR);
		$query->bindValue(':image', $this->image, PDO::PARAM_STR);
		$query->execute();
		$this->id = $pdo->lastInsertId();
	}

	function update()
	{
		$sql = "UPDATE theme SET nom=:nom, description=:description, image=:image WHERE id=:id";
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $this->id, PDO::PARAM_INT);
		$query->bindValue(':nom', $this->nom, PDO::PARAM_STR);
		$query->bindValue(':description', $this->description, PDO::PARAM_STR);
		$query->bindValue(':image', $this->image, PDO::PARAM_STR);
		$query->execute();
	}

	function delete()
	{
		// Suppression du fichier lié
		if (!empty($this->image)) unlink('upload/' . $this->image);

		// Suppression du thème
		$sql = "DELETE FROM theme WHERE id=:id";
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $this->id, PDO::PARAM_INT);
		$query->execute();
	}

	function chargePOST()
	{
		$this->id = postInt('id');
		$this->nom = postString('nom');
		$this->description = postString('description');
		$this->image = postString('old-image');

		// Récupère les informations sur le fichier uploadés si il existe
		$image = chargeFILE();
		if (!empty($image)) {
			// Supprime l'ancienne image si update
			unlink('upload/' . $this->image);
			$this->image = $image;
		}
	}



	
	static function controleur($action, $id, &$view, &$data)
{
    // Ajout de la récupération des cours dans chaque cas où tu veux afficher les cours
    $liste_cours = Cours::readAll(); // Récupère tous les cours

    switch ($action) {
        case 'catalogue':
            $view = 'catalogue.twig';
            $data = [
                'liste_themes' => Theme::readAll(), // Récupère tous les thèmes
                'liste_cours' => $liste_cours       // Ajoute les cours pour le carousel
            ];
            break;

        case 'fiche-theme':
            if ($id > 0) {
                $theme = Theme::readOne($id);
                $articles = Article::readAllByTheme($id);
                $view = 'fiche_theme.twig';
                $data = [
                    'theme' => $theme,
                    'articles' => $articles,
                    'liste_cours' => $liste_cours // Ajoute les cours à la fiche du thème
                ];
            } else {
                $view = 'catalogue.twig';
                $data = [
                    'liste_themes' => Theme::readAll(),
                    'liste_cours' => $liste_cours // Ajoute les cours même en cas d'ID incorrect
                ];
            }
            break;
		
        default:
            $view = 'visit_themes.twig';
            $data = [
                'liste_themes' => Theme::readAll(),
                'liste_cours' => $liste_cours // Ajoute les cours pour la vue par défaut
            ];
            break;
    }
}

	
	
	static function controleurAdmin($action, $id, &$view, &$data)
	{
		switch ($action) {
			case 'read':
				if ($id > 0) {
					$view = 'theme/detail_theme.twig';
					$data = [
						'theme' => Theme::readOne($id),
						'liste_articles' => Article::readAllByTheme($id)
					];
				} else {
					$view = 'theme/liste_themes.twig';
					$data = ['liste_themes' => Theme::readAll()];
				}
				break;
			case 'new':
				$view = "theme/form_theme.twig";
				break;
			case 'create':
				$theme = new Theme();
				$theme->chargePOST();
				$theme->create();
				header('Location: admin.php?page=theme');
				break;
			case 'edit':
				$view = "theme/edit_theme.twig";
				$data = ['theme' => Theme::readOne($id)];
				break;
			case 'update':
				$theme = new Theme();
				$theme->chargePOST();
				$theme->update();
				header('Location: admin.php?page=theme');
				break;
			case 'delete':
				$theme = Theme::readOne($id);
				$theme->delete();
				header('Location: admin.php?page=theme');
				break;
			default:
				$view = 'theme/liste_themes.twig';
				$data = ['liste_themes' => Theme::readAll()];
				break;
		}
	}

	static function controleurPro($action, $id, &$view, &$data)
	{
		switch ($action) {
			case 'read':
				if ($id > 0) {
					$view = 'theme_pro/detail_theme.twig';
					$data = [
						'theme' => Theme::readOne($id),
						'liste_articles' => Article::readAllByTheme($id)
					];
				} else {
					$view = 'theme_pro/liste_themes.twig';
					$data = ['liste_themes' => Theme::readAll()];
				}
				break;
			case 'new':
				$view = "theme_pro/form_theme.twig";
				break;
			case 'create':
				$theme = new Theme();
				$theme->chargePOST();
				$theme->create();
				header('Location: professionnel.php?page=theme');
				break;
			case 'edit':
				$view = "theme_pro/edit_theme.twig";
				$data = ['theme' => Theme::readOne($id)];
				break;
			case 'update':
				$theme = new Theme();
				$theme->chargePOST();
				$theme->update();
				header('Location: professionnel.php?page=theme');
				break;
			default:
				$view = 'theme_pro/liste_themes.twig';
				$data = ['liste_themes' => Theme::readAll()];
				break;
		}
	}

	

	// Création de la table themes
	static function init()
	{
		// connexion
		$pdo = connexion();

		// suppression des données existantes le cas échéant
		$sql = 'drop table if exists theme';
		$query = $pdo->prepare($sql);
		$query->execute();

		// création de la table 'theme'
		$sql = 'create table theme (
				id serial primary key,
				nom varchar(128),
				description varchar(512),
				image varchar(512))';
		$query = $pdo->prepare($sql);
		$query->execute();
	}
}

