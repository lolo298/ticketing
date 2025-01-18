<?php

namespace ORM;

use Runtime\BDD;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Runtime\Manager;
use Ticketing\Models\State;
use Ticketing\Models\Traitement;

class Entity {
  private string $TABLE_NAME = "";
  /** @var ReflectionProperty[] */
  private array $columns = [];
  /** @var ReflectionProperty[] */
  private array $relationsManyToOne = [];
  /** @var ReflectionProperty[] */
  private array $relationsManyToMany = [];

  private mixed $idField;

  public function __construct($data = []) {
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
    })[0];

    $this->columns = $cols;
    $this->relationsManyToOne = $relationsManyToOne;
    $this->relationsManyToMany = $relationsManyToMany;

    if (!empty($data)) {
      if (array_key_exists($id->getName(), $data)) {
        $method = "set" . ucfirst($id->getName());
        $this->$method($data[$id->getName()]);
        $this->idField = $data[$id->getName()];
      } else {
        $this->idField = null;
      }

      $this->hydrate($data);
    } else {
      $this->idField = null;
    }
  }

  public function save($circular = false): void {
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
      $attr = $attrs[0]->newInstance();


      if ($relation->isInitialized($this) === false || $relation->getValue($this) === null) {
        if ($this::class === $attr->inversedBy && $circular === false) {
          throw new \Exception("Relation " . $relation->getName() . " cannot be null");
        }
      }
    }

    $db->beginTransaction();


    if ($this->idField !== null) {
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
        $setter = "set" . ucfirst($name);

        $methodReflection = new ReflectionMethod($this, $setter);
        $arg = $methodReflection->getParameters()[0];

        if (!$arg->getType()->isBuiltin() && $arg->getType()->getName() !== "DateTime") {
          $val = $this->$method();
          $val->save();
          $val = $val->getId();

          // $val = new ($colAttr->type)();
        } else {
          $val = $this->$method();
        }



        if ($colAttr->type === ColumnType::DATETIME) {
          $val = $val->format("Y-m-d H:i:s");
        }

        $vals[$dbName] = $val;
      }

      foreach ($this->relationsManyToOne as $relation) {
        if ($circular) {
          continue;
        }
        $propName = $relation->getName();
        $attrs = $relation->getAttributes(ManyToOne::class);
        $attr = $attrs[0]->newInstance();

        $method = "get" . ucfirst($propName);
        $val = $this->$method();

        if ($attr->inversedBy === $this::class) {
          $val->save(true);

          $reflectRelation = new ReflectionClass($val);
          $propsRelation = $reflectRelation->getProperties();
          $pk = array_filter($propsRelation, function ($prop) {
            $args = $prop->getAttributes(Id::class);
            return !empty($args);
          });
          $pkName = $pk[0]->getName();
          $method = "get" . ucfirst($pkName);
          $vals["id_" . $propName] = $val->$method();
        } else {
          foreach ($val as $v) {
            $v->save(true);
          }
        }
      }

      $sql = "UPDATE " . $this->TABLE_NAME . " SET ";

      foreach ($vals as $name => $value) {
        $sqls[] = "$name = :$name";
      }

      $sql .= join(", ", $sqls) . " WHERE id = :id";
      $stmt = $pdo->prepare($sql);

      $stmt->bindValue(":id", $this->idField);

      foreach ($vals as $name => $value) {
        if (is_bool($value)) {
          $value = $value ? 1 : 0;
        }
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
        if ($circular) {
          continue;
        }

        $propName = $relation->getName();
        $method = "get" . ucfirst($propName);
        $val = $this->$method();
        $attrs = $relation->getAttributes(ManyToOne::class);
        $attr = $attrs[0]->newInstance();

        if ($attr->inversedBy !== $this::class) {
          //inverser par la target donc self = [T] target = T

          foreach ($val as $v) {
            $v->save(true);
          }
        } else {
          //inverser par la self donc self = T target = [T]

          $val->save(true);
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
      }

      $valsKeys = array_keys($vals);
      $valsKeys = array_map(function ($key) {
        return str_replace("_", "", $key);
      }, $valsKeys);

      $sql = "INSERT INTO " . $this->TABLE_NAME . " (" . join(", ", $props) . ") VALUES (" . join(", ", $valsKeys) . ")";

      try {

        $stmt = $pdo->prepare($sql);

        foreach ($vals as $key => $val) {
          $val = $val === null ? "" : $val;
          if (is_bool($val)) {
            $val = $val ? 1 : 0;
          }

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


      foreach ($this->relationsManyToMany as $relation) {
        $propName = $relation->getName();
        $attrs = $relation->getAttributes(ManyToMany::class);
        $attr = $attrs[0]->newInstance();
        $targetEntity = new ReflectionClass($attr->targetEntity);
        $mainEntity = new ReflectionClass($attr->mainEntity);

        $tableName = "assoc_" . $mainEntity->getShortName() . "_" . $targetEntity->getShortName();

        $sql = "INSERT INTO $tableName (id_" . lcfirst($mainEntity->getShortName()) . ", id_" . lcfirst($targetEntity->getShortName()) . ") VALUES (:id_" . lcfirst($mainEntity->getShortName()) . ", :id_" . lcfirst($targetEntity->getShortName()) . ")";

        $method = "get" . ucfirst($propName);

        foreach ($this->$method() as $val) {
          $val->save();

          $valId = $val->getId();

          $stmt = $pdo->prepare($sql);
          $stmt->bindValue(":id_" . lcfirst($mainEntity->getShortName()), $id);
          $stmt->bindValue(":id_" . lcfirst($targetEntity->getShortName()), $valId);
          $stmt->execute();
        }
      }

      $db->commit();
    }
  }

  public function hydrate(array $data, array $circularReferences = []): void {
    $reflect = new ReflectionClass($this);
    $props = $reflect->getProperties();

    foreach ($this->columns as $prop) {
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
            default => (function () use ($val, $col) {
              $reflect = new ReflectionClass($col->type);

              $manager = new Manager();
              $data = $manager->findById($val, $reflect->getShortName(), (string)$col->type);
              return $data;
            })()
          };

          $this->$method($val);
        }
      }
    }

    foreach ($this->relationsManyToOne as $relation) {
      $relationName = $relation->getName();
      $attrs = $relation->getAttributes(ManyToOne::class);
      $manyToOne = $attrs[0]->newInstance();

      $relationClass = $manyToOne->targetEntity;
      $targetTableName = (new ReflectionClass($relationClass))->getShortName();

      if ($manyToOne->inversedBy !== $this::class) {
        if (in_array($this::class, $circularReferences)) {
          continue;
        }
        if (!array_key_exists("id", $data)) {
          continue;
        }

        $sql = "SELECT * FROM $targetTableName WHERE id_" . $reflect->getShortName() . " = :id";
        $stmt = Manager::getInstance()->prepare($sql);
        $stmt->bindValue(":id", $data["id"]);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $relationInstances = [];
        $circularReferences[] = $relationClass;
        if ($rows !== false) {
          foreach ($rows as $row) {
            $relationInstance = new $relationClass();
            $relationInstance->hydrate($row, array_merge($circularReferences, [$relationClass]));
            $relationInstances[] = $relationInstance;
          }
        }

        $method = "set" . ucfirst($relationName);
        $this->$method($relationInstances);
      } else {
        if (isset($data['id_' . $relationName])) {
          if (in_array($this::class, $circularReferences)) {
            continue;
          }
          $sql = "SELECT * FROM $targetTableName WHERE id = :id LIMIT 1";
          $stmt = Manager::getInstance()->prepare($sql);
          $stmt->bindValue(":id", $data["id_" . $relationName]);
          $stmt->execute();
          $row = $stmt->fetch(\PDO::FETCH_ASSOC);

          $relationInstance = new $relationClass($row);
          $method = "set" . ucfirst($relationName);
          $this->$method($relationInstance);
        }
      }
    }

    foreach ($this->relationsManyToMany as $relation) {
      $attrs = $relation->getAttributes(ManyToMany::class);
      $attr = $attrs[0]->newInstance();

      $relationName = (new ReflectionClass($attr->targetEntity))->getShortName();
      $targetEntityClass = $attr->targetEntity;
      // $tableName = "assoc_" . $relationName . "_" . $reflect->getShortName();

      $mainEntity = new ReflectionClass($attr->mainEntity);
      $targetEntity = new ReflectionClass($attr->targetEntity);

      $tableName = "assoc_" . $mainEntity->getShortName() . "_" . $targetEntity->getShortName();

      if (in_array($targetEntityClass, $circularReferences)) {
      } else {
        $circularSql = "SELECT DISTINCT main.* FROM " . $targetEntity->getShortName() . " main JOIN $tableName assocTable ON main.id = assocTable.id_" . $targetEntity->getShortName() . " WHERE assocTable.id_" . $reflect->getShortName() . " = :id";
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

        $circularReferences[] = $targetEntityClass;
        foreach ($data as $row) {
          $className = $targetEntity->getName();
          $relationInstance = new $className();
          $relationInstance->hydrate($row, array_merge($circularReferences, [$targetEntityClass]));
          $hydratedData[] = $relationInstance;
        }

        $method = "set" . ucfirst($relation->getName());
        if (method_exists($this, $method)) {
          $this->$method($hydratedData);
        }
      }
    }
  }
}
