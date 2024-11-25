<?php

namespace ORM;

use Runtime\BDD;

use \Attribute;
use ReflectionProperty;
use Runtime\Manager;

class Entity {
  private string $TABLE_NAME = "";
  /** @var ReflectionProperty[] */
  private array $columns = [];
  /** @var ReflectionProperty[] */
  private array $relationsManyToOne = [];
  /** @var ReflectionProperty[] */
  private array $relationsManyToMany = [];

  public function __construct() {
    $className = get_class($this);
    $tmp = explode("\\", $className);
    $this->TABLE_NAME = array_pop($tmp);

    $reflect = new \ReflectionClass($this);
    $props = $reflect->getProperties();
    $cols = array_filter($props, function ($prop) {
      $args = $prop->getAttributes(Column::class);
      return !empty($args);
    });
    $relationsManyToOne = array_filter($props, function ($prop) {
      $args = $prop->getAttributes(ManyToOne::class);
      return !empty($args);
    });
    $relationsManyToMany = array_filter($props, function ($prop) {
      $args = $prop->getAttributes(ManyToMany::class);
      return !empty($args);
    });


    $this->columns = $cols;
    $this->relationsManyToOne = $relationsManyToOne;
    $this->relationsManyToMany = $relationsManyToMany;
  }

  public function save(): void {
    $db = BDD::getInstance();
    $pdo = $db->getPDO();

    $id = array_filter($this->columns, function ($col) {
      $args = $col->getAttributes(Id::class);
      return !empty($args);
    });


    //checks nullable
    foreach ($this->columns as $col) {
      $attrs = $col->getAttributes(Column::class);
      $colAttr = $attrs[0]->newInstance();

      if ($colAttr->default === false && $colAttr->nullable === false && $col->isInitialized($this) === false) {
        throw new \Exception("Column " . $col->getName() . " cannot be null");
      }
    }

    foreach ($this->relationsManyToOne as $relation) {
      $attrs = $relation->getAttributes(ManyToOne::class);

      if ($relation->isInitialized($this) === false || $relation->getValue($this) === null) {
        throw new \Exception("Relation " . $relation->getName() . " cannot be null");
      }
    }

    $db->beginTransaction();

    if (!empty($id) && $id[0]->isInitialized($this)) {
      //update
    } else {
      //insert

      $props = [];
      $vals = [];

      foreach ($this->columns as $col) {
        $colAttrs = $col->getAttributes(Column::class);
        $colAttr = $colAttrs[0]->newInstance();

        if ($col->isInitialized($this) === false && $colAttr->default) {
          continue;
        }


        $r = "/(?=[A-Z])/";
        $dbName = $col->getName();
        $dbName = preg_replace($r, "_", $dbName);
        $dbName = strtolower($dbName);


        $name = strtolower($col->getName());
        $method = "get" . ucfirst($name);
        $val = $this->$method();

        if ($colAttr->type === ColumnType::DATETIME) {
          $val = $val->format("Y-m-d H:i:s");
        }

        $props[] = $dbName;
        $vals[":$dbName"] = $val;
      }

      foreach ($this->relationsManyToOne as $relation) {
        $propName = $relation->getName();
        $method = "get" . ucfirst($propName);
        $val = $this->$method();
        $val->save();

        $reflectRelation = new \ReflectionClass($val);
        $propsRelation = $reflectRelation->getProperties();
        $pk = array_filter($propsRelation, function ($prop) {
          $args = $prop->getAttributes(Id::class);
          return !empty($args);
        });


        $pkName = $pk[0]->getName();
        $method = "get" . ucfirst($pkName);

        $props[] = "id_" . $propName;
        $vals[":id_" . $propName] = $val->$method();
      }

      $valsKeys = array_keys($vals);
      $valsKeys = array_map(function ($key) {
        return str_replace("_", "", $key);
      }, $valsKeys);

      $sql = "INSERT INTO " . $this->TABLE_NAME . " (" . join(", ", $props) . ") VALUES (" . join(", ", $valsKeys) . ")";

      try {

        $stmt = $pdo->prepare($sql);

        foreach ($vals as $key => $val) {
          $val = $val == null ? "" : $val;
          $stmt->bindValue(str_replace("_", "", $key), $val);
        }

        $stmt->execute();

        $id = $pdo->lastInsertId();


        $reflect = new \ReflectionClass($this);
        $props = $reflect->getProperties();
        $pk = array_filter($props, function ($prop) {
          $args = $prop->getAttributes(Id::class);
          return !empty($args);
        });

        $pkName = $pk[0]->getName();
        $method = "set" . ucfirst($pkName);
        $this->$method($id);
      } catch (\PDOException $e) {
        $db->rollBack();
        die($e->getMessage());
      }

      $db->commit();
    }
  }

  public function hydrate(array $data, array $circularReferences = [], ?Entity $parent = null): void {
    $reflect = new \ReflectionClass($this);
    $props = $reflect->getProperties();

    foreach ($props as $prop) {
      $propName = $prop->getName();

      $r = "/(?=[A-Z])/";
      $name = preg_replace($r, "_", $propName);
      $name = strtolower($name);

      if (array_key_exists($name, $data)) {
        $method = "set" . ucfirst($propName);
        if (method_exists($this, $method)) {
          //parse data
          $val = $data[$name];
          $attrs = $prop->getAttributes(Column::class);
          if (empty($attrs)) {
            $this->$method($val);
            continue;
          }
          
          $col = $attrs[0]->newInstance();
          if ($val === null && $col->nullable === false) {
            throw new \Exception("Column $name cannot be null");
          }

          $val = match ($col->type) {
            ColumnType::VARCHAR => (string) $val,
            ColumnType::INTEGER => (int) $val,
            ColumnType::BOOLEAN => (bool) $val,
            ColumnType::DATETIME => new \DateTime($val),
            ColumnType::DECIMAL => (float) $val,
            default => $val
          };

          $this->$method($val);
        }
      } else {
      }
    }

    foreach ($this->relationsManyToOne as $relation) {
      $relationName = $relation->getName();
      $relationClass = $relation->getName();
      if (isset($data[$relationName])) {
        $relationInstance = new $relationClass();
        $relationInstance->hydrate($data[$relationName], $circularReferences, $this);
        $method = "set" . ucfirst($relationName);
        if (method_exists($this, $method)) {
          $this->$method($relationInstance);
        }
      }
    }

    foreach ($this->relationsManyToMany as $relation) {
      $relationName = $relation->getName();
      $relationClass = $relation->getType()->getName();
      $tableName = "assoc_" . $relationName . "_" . $reflect->getShortName();

      if (in_array($relationClass, $circularReferences)) {
        foreach ($circularReferences as $data) {
          $this->$method($circularReferences);
        }
      } else {
        $circularSql = "SELECT DISTINCT main.* FROM " . $reflect->getShortName() . " main JOIN $tableName assocTable ON main.id = assocTable.id_" . $reflect->getShortName() . " WHERE assocTable.id_" . $relationClass . " = :id";
        $stmt = Manager::getInstance()->prepare($circularSql);

        $id = 0;

        foreach ($props as $prop) {
          $attrs = $prop->getAttributes(Id::class);
          if (!empty($attrs)) {
            $idName = $prop->getName();
            $method = "get" . ucfirst($idName);
            $id = $this->$method();
            break;
          }
        }

        $stmt->bindValue("id", $id);
        $stmt->execute();

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $hydratedData = [];

        foreach ($data as $row) {
          $className = $reflect->getName();
          $relationInstance = new $className();
          $relationInstance->hydrate($row, array_merge($circularReferences, [$this]), $this);
          $hydratedData[] = $relationInstance;
        }

        $method = "set" . ucfirst($relationName);
        if (method_exists($this, $method)) {
          $this->$method($hydratedData);
        }
      }
    }
  }
}
