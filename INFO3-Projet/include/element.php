<?php

use phpDocumentor\Reflection\DocBlock\Tags\Var_;

require_once('fonctions.php');

class Element
{
	public $id;
	public $ordre;
	public $type;
	public $contenu;
	public $image;
	public $id_article;

	// Le constructeur corrige les données récupérées de la BDD
	// Ici convertie les clés et l'ordre (pour le tri) en entier
	function __construct()
	{
		$this->id = intval($this->id);
		$this->ordre = intval($this->ordre);
		$this->id_article = intval($this->id_article);
	}

	static function readOne($id)
	{
		$sql = 'SELECT * FROM element WHERE id = :id';
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
		return $query->fetchObject('Element');
	}

	// Récupère les elements d'un thème
	static function readAllByArticle($id)
	{
		$sql = 'SELECT * FROM element WHERE id_article = :id ORDER BY ordre';
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll(PDO::FETCH_CLASS, 'Element');
	}

	// Récupère l'ordre maximal des elements d'un thème
	static function readOrderMax($id)
	{
		$sql = 'SELECT max(ordre) AS maximum FROM element WHERE id_article = :id';
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
		$objet = $query->fetchObject();
		return intval($objet->maximum);
	}

	// Echange l'ordre de deux elements
	function exchangeOrder()
	{
		// Recherche l'element précédent (dans le même thème)
		// C'est l'element le plus grand, parmi les elements d'ordre inférieur

		// étape 1 : cherche les elements du même thème ayant un ordre inférieur
		$sql = 'SELECT * FROM element
				WHERE id_article = :id_article AND ordre < :ordre ORDER BY ordre DESC';
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id_article', $this->id_article, PDO::PARAM_INT);
		$query->bindValue(':ordre', $this->ordre, PDO::PARAM_INT);
		$query->execute();

		// étape 2 : les elements sont triés par ordre décroissant
		// donc le premier element est le plus grand des plus petits, donc le précédent
		$before = $query->fetchObject('Element');

		// Si le précédent existe (l'element courant n'est pas le premier)
		if ($before) {
			// Échange les valeurs d'ordre et enregistre dans la BDD
			$tmp = $this->ordre;
			$this->ordre = $before->ordre;
			$this->update();
			$before->ordre = $tmp;
			$before->update();
		}
	}

	function create()
	{
		// Récupère l'ordre maximum pour créer l'element en dernière position
		$maximum = self::readOrderMax($this->id_article);
		$this->ordre = $maximum + 1;

		$sql = "INSERT INTO element (ordre, type, contenu, image, id_article)
				VALUES (:ordre, :type, :contenu, :image, :id_article)";
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':ordre', $this->ordre, PDO::PARAM_INT);
		$query->bindValue(':type', $this->type, PDO::PARAM_STR);
		$query->bindValue(':contenu', $this->contenu, PDO::PARAM_STR);
		$query->bindValue(':image', $this->image, PDO::PARAM_STR);
		$query->bindValue(':id_article', $this->id_article, PDO::PARAM_INT);
		$query->execute();
		$this->id = $pdo->lastInsertId();
	}

	function update()
	{
		$sql = "UPDATE element
				SET ordre=:ordre, type=:type, contenu=:contenu, image=:image
				WHERE id=:id";
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $this->id, PDO::PARAM_INT);
		$query->bindValue(':ordre', $this->ordre, PDO::PARAM_INT);
		$query->bindValue(':type', $this->type, PDO::PARAM_STR);
		$query->bindValue(':contenu', $this->contenu, PDO::PARAM_STR);
		$query->bindValue(':image', $this->image, PDO::PARAM_STR);
		$query->execute();
	}

	function delete()
	{
		// Suppression du fichier lié
		if (!empty($this->image)) unlink('upload/' . $this->image);

		$sql = "DELETE FROM element WHERE id=:id";
		$pdo = connexion();
		$query = $pdo->prepare($sql);
		$query->bindValue(':id', $this->id, PDO::PARAM_INT);
		$query->execute();
	}

	function chargePOST()
	{
		$this->id = postInt('id');
		$this->ordre = postInt('ordre');
		$this->type = postString('type');
		$this->contenu = postString('contenu');
		$this->image = postString('old-image');
		$this->id_article = postInt('id_article');

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
		switch ($action) {
			default:
				$view = 'visit_element.twig';
				$data = [
					'element' => Element::readOne($id)
				];
				break;
		}
	}

	static function controleurAdmin($action, $id, &$view, &$data)
	{
		switch ($action) {
			case 'read':
				// Pas d'affichage d'un élément seul => retour à l'article
				header('Location: admin.php?page=article&id=' . $id);
				break;
			case 'new':
				$view = "element/form_element.twig";
				$data = ['id_article' => $id];
				break;
			case 'create':
				$element = new Element();
				$element->chargePOST();
				$element->create();
				header('Location: admin.php?page=article&id=' . $element->id_article);
				break;
			case 'edit':
				$view = "element/edit_element.twig";
				$data = ['element' => Element::readOne($id)];
				break;
			case 'update':
				$element = new Element();
				$element->chargePOST();
				$element->update();
				header('Location: admin.php?page=article&id=' . $element->id_article);
				break;
			case 'delete':
				$element = Element::readOne($id);
				$element->delete();
				header('Location: admin.php?page=article&id=' . $element->id_article);
				break;
			case 'exchange':
				$element = Element::readOne($id);
				$element->exchangeOrder();
				header('Location: admin.php?page=article&id=' . $element->id_article);
				break;
			default:
				// Pas d'action connue => retour à la page article
				header('Location: admin.php?page=article&id=' . $id);
		}
	}

	static function controleurPro($action, $id, &$view, &$data)
	{
		switch ($action) {
			case 'read':
				// Pas d'affichage d'un élément seul => retour à l'article
				header('Location: professionnel.php?page=article&id=' . $id);
				break;
			case 'new':
				$view = "element/form_element.twig";
				$data = ['id_article' => $id];
				break;
			case 'create':
				$element = new Element();
				$element->chargePOST();
				$element->create();
				header('Location:  professionnel.php?page=article&id=' . $element->id_article);
				break;
			case 'edit':
				$view = "element/edit_element.twig";
				$data = ['element' => Element::readOne($id)];
				break;
			case 'update':
				$element = new Element();
				$element->chargePOST();
				$element->update();
				header('Location:  professionnel.php?page=article&id=' . $element->id_article);
				break;
			case 'exchange':
				$element = Element::readOne($id);
				$element->exchangeOrder();
				header('Location:  professionnel.php?page=article&id=' . $element->id_article);
				break;
			default:
				// Pas d'action connue => retour à la page article
				header('Location:  professionnel.php?page=article&id=' . $id);
		}
	}

	// Création de la table themes
	static function init()
	{
		// connexion
		$pdo = connexion();

		// suppression des données existantes le cas échéant
		$sql = 'drop table if exists element';
		$query = $pdo->prepare($sql);
		$query->execute();

		// création de la table 'theme'
		$sql = 'create table element (
				id serial primary key,
				ordre int,
				type varchar(128),
				contenu text,
				image varchar(512),
				id_article bigint unsigned,
    			foreign key (id_article) references article(id))';
		$query = $pdo->prepare($sql);
		$query->execute();
	}
}
