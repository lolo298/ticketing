<?php

namespace ORM;

use Runtime\BDD;

use \Attribute;
use ReflectionClass;
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

  private mixed $id;

  public function __construct($data = []) {
    if (!empty($data)) {
      $this->hydrate($data);
    }


    $className = get_class($this);
    $tmp = explode("\\", $className);
    $this->TABLE_NAME = array_pop($tmp);

    $reflect = new ReflectionClass($this);
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

    $id = array_filter($props, function ($prop) {
      $args = $prop->getAttributes(Id::class);
      return !empty($args);
    });

    if (!empty($id) && $id[0]->isInitialized($this)) {
      $name = $id[0]->getName();
      $method = "get" . ucfirst($name);
      $this->id = $this->$method();
    } else {
      $this->id = null;
    }

    $this->columns = $cols;
    $this->relationsManyToOne = $relationsManyToOne;
    $this->relationsManyToMany = $relationsManyToMany;

    if (!empty($data)) {
      $this->hydrate($data);
    }
  }

  public function save(): void {
    $db = BDD::getInstance();
    $pdo = $db->getPDO();


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

    if ($this->id !== null) {
      //update

      $vals = [];
      $sqls = [];

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

        $vals[$dbName] = $val;
      }

      foreach ($this->relationsManyToOne as $relation) {
        $propName = $relation->getName();
        $method = "get" . ucfirst($propName);
        $val = $this->$method();
        $val->save();

        $reflectRelation = new ReflectionClass($val);
        $propsRelation = $reflectRelation->getProperties();
        $pk = array_filter($propsRelation, function ($prop) {
          $args = $prop->getAttributes(Id::class);
          return !empty($args);
        });


        $pkName = $pk[0]->getName();
        $method = "get" . ucfirst($pkName);

        $vals["id_" . $propName] = $val->$method();
      }


      $sql = "UPDATE " . $this->TABLE_NAME . " SET ";

      foreach ($vals as $name => $value) {
        $sqls[] = "$name = :$name";
      }

      $sql .= join(", ", $sqls) . " WHERE id = :id";
      $stmt = $pdo->prepare($sql);

      $stmt->bindValue(":id", $this->id);

      foreach ($vals as $name => $value) {
        $stmt->bindValue($name, $value);
      }

      try {
        $stmt->execute();
        $db->commit();
      } catch (\Exception $e) {
        $db->rollBack();
        throw $e;
      }
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

        $reflectRelation = new ReflectionClass($val);
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


        $reflect = new ReflectionClass($this);
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

  public function hydrate(array $data, array $circularReferences = []): void {
    $reflect = new ReflectionClass($this);
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
      $attrs = $relation->getAttributes(ManyToOne::class);
      $manyToOne = $attrs[0]->newInstance();

      $relationClass = $manyToOne->targetEntity;


      if (isset($data['id_' . $relationName])) {
        $sql = "SELECT * FROM $relationName WHERE id = :id LIMIT 1";
        $stmt = Manager::getInstance()->prepare($sql);
        $stmt->bindValue(":id", $data["id_". $relationName]);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $relationInstance = new $relationClass($row);
        $method = "set" . ucfirst($relationName);
        $this->$method($relationInstance);
      }
    }

    foreach ($this->relationsManyToMany as $relation) {
      $relationName = $relation->getName();
      $relationClass = $relation->getType()->getName();
      // $tableName = "assoc_" . $relationName . "_" . $reflect->getShortName();

      $attrs = $relation->getAttributes(ManyToMany::class);
      $attr = $attrs[0]->newInstance();

      $mainEntity = new ReflectionClass($attr->mainEntity);
      $targetEntity = new ReflectionClass($attr->targetEntity);

      $tableName = "assoc_" . $mainEntity->getShortName() . "_" . $targetEntity->getShortName();

      if (in_array($relationClass, $circularReferences)) {
      } else {
        $circularSql = "SELECT DISTINCT main.* FROM " . $reflect->getShortName() . " main JOIN $tableName assocTable ON main.id = assocTable.id_" . $reflect->getShortName() . " WHERE assocTable.id_" . $reflect->getShortName() . " = :id";
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

        $circularReferences[] = $relationClass;
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
