<?php

require_once('fonctions.php');

class Cours
{
    public $id;
    public $titre;
    public $description;
    public $difficulte; 
    public $places_restantes;
    public $prix;
 

    function __construct()
    {
        $this->id = intval($this->id);
        $this->places_restantes = intval($this->places_restantes);
    }

    static function readAll()
    {
        $sql = 'SELECT * FROM cours';
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'Cours');
    }

    static function readOne($id)
    {
        $sql = 'SELECT * FROM cours WHERE id = :id';
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchObject('Cours');
    }

    function create()
    {
        $sql = "INSERT INTO cours (titre, description, difficulte, places_restantes) 
                VALUES (:titre, :description,:difficulte , :places_restantes)";
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->bindValue(':titre', $this->titre, PDO::PARAM_STR);
        $query->bindValue(':description', $this->description, PDO::PARAM_STR);
        $query->bindValue(':difficulte', $this->difficulte, PDO::PARAM_STR);
        $query->bindValue(':places_restantes', $this->places_restantes, PDO::PARAM_INT);
        $query->execute();
        $this->id = $pdo->lastInsertId();
    }

    function update()
    {
        $sql = "UPDATE cours SET titre=:titre, description=:description, 
                difficulte=:difficulte, places_restantes=:places_restantes, 
                prix=:prix WHERE id=:id";
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $this->id, PDO::PARAM_INT);
        $query->bindValue(':titre', $this->titre, PDO::PARAM_STR);
        $query->bindValue(':description', $this->description, PDO::PARAM_STR);
        $query->bindValue(':difficulte', $this->difficulte, PDO::PARAM_STR);
        $query->bindValue(':places_restantes', $this->places_restantes, PDO::PARAM_INT);
        $query->bindValue(':prix', $this->prix, PDO::PARAM_STR); // Nouvelle ligne
        $query->execute();
    }
    
    function delete()
    {
        $sql = "DELETE FROM cours WHERE id=:id";
        $pdo = connexion();
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $this->id, PDO::PARAM_INT);
        $query->execute();
    }

    function chargePOST()
    {
        $this->id = postInt('id');
        $this->titre = postString('titre');
        $this->description = postString('description');
        $this->difficulte = postString('difficulte');
        $this->places_restantes = postInt('places_restantes');
        $this->prix = postFloat('prix'); // Nouvelle ligne
    }
    

    public static function search($difficulte, $places_min, $places_max)
    {
        $pdo = connexion();
        $sql = "SELECT * FROM cours WHERE 1=1";
        $params = [];

        if (!empty($difficulte)) {
            $sql .= " AND difficulte = :difficulte";
            $params[':difficulte'] = $difficulte;
        }

        if (!empty($places_min)) {
            $sql .= " AND places_restantes >= :places_min";
            $params[':places_min'] = $places_min;
        }

        if (!empty($places_max)) {
            $sql .= " AND places_restantes <= :places_max";
            $params[':places_max'] = $places_max;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Cours');
    }

    static function controleurAdmin($action, $id, &$view, &$data)
    {
        switch ($action) {
            case 'detail':
                if ($id > 0) {
                    $view = 'cours/detail.twig';
                    $data = ['cours' => Cours::readOne($id)];
                } else {
                    header('Location: admin.php?page=cours');
                    exit;
                }
                break;

            case 'read':
                if ($id > 0) {
                    $view = 'cours/detail.twig';
                    $data = ['cours' => Cours::readOne($id)];
                } else {
                    $view = 'cours/liste.twig';
                    $data = ['liste_cours' => Cours::readAll()];
                }
                break;

            case 'new':
                $view = "cours/form.twig";
                break;

            case 'create':
                $cours = new Cours();
                $cours->chargePOST();
                $cours->create();
                header('Location: admin.php?page=cours');
                break;

            case 'edit':
                $view = "cours/edit.twig";
                $data = ['cours' => Cours::readOne($id)];
                break;

            case 'update':
                $cours = new Cours();
                $cours->chargePOST();
                $cours->update();
                header('Location: admin.php?page=cours');
                break;

            case 'delete':
                $cours = Cours::readOne($id);
                $cours->delete();
                header('Location: admin.php?page=cours');
                break;

            default:
                $view = 'cours/liste.twig';
                $data = ['liste_cours' => Cours::readAll()];
                break;
        }
    }
    static function controleurPro($action, $id, &$view, &$data)
    {
        switch ($action) {
            case 'detail':
                if ($id > 0) {
                    $view = 'cours_pro/detail.twig';
                    $data = ['cours' => Cours::readOne($id)];
                } else {
                    header('Location: professionnel.php?page=cours');
                    exit;
                }
                break;

            case 'read':
                if ($id > 0) {
                    $view = 'cours_pro/detail.twig';
                    $data = ['cours' => Cours::readOne($id)];
                } else {
                    $view = 'cours_pro/liste.twig';
                    $data = ['liste_cours' => Cours::readAll()];
                }
                break;

            case 'new':
                $view = "cours_pro/form.twig";
                break;

            case 'create':
                $cours = new Cours();
                $cours->chargePOST();
                $cours->create();
                header('Location: professionnel.php?page=cours');
                break;

            case 'edit':
                $view = "cours_pro/edit.twig";
                $data = ['cours' => Cours::readOne($id)];
                break;

            case 'update':
                $cours = new Cours();
                $cours->chargePOST();
                $cours->update();
                header('Location: professionnel.php?page=cours');
                break;

            default:
                $view = 'cours_pro/liste.twig';
                $data = ['liste_cours' => Cours::readAll()];
                break;
        }
    }


}
