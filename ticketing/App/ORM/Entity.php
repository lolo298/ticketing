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

    echo '<pre>';
    var_dump($this->relationsManyToOne);
    echo '</pre>';
  }

  public function save(): void {
    $db = BDD::getInstance();

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
      $relAttr = $attrs[0]->newInstance();
      echo "<pre>";
      var_dump($relation->isInitialized($this));
      echo "</pre>";
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
        echo "checking column " . $col->getName() . "<br>";
        $colAttrs = $col->getAttributes(Column::class);
        $colAttr = $colAttrs[0]->newInstance();

        if ($col->isInitialized($this) === false && $colAttr->default) {
          echo "uninitialized column with default value<br>";
          continue;
        }

        echo "binding column " . $col->getName() . "<br>";


        $name = strtolower($col->getName());
        $method = "get" . ucfirst($name);
        $val = $this->$method();



        if ($colAttr->type === "datetime") {
          $val = $val->format("Y-m-d H:i:s");
        }
        // $stmt->bindValue(":$name", $val);
        $props[] = $name;
        $vals[":$name"] = $val;
      }

      foreach ($this->relationsManyToOne as $relation) {
        $propName = $relation->getName();
        $method = "get" . ucfirst($propName);
        $val = $this->$method();
        $val->save();


        $reflectRelation = new \ReflectionClass($this->$propName);
        $propsRelation = $reflectRelation->getProperties();
        $pk = array_filter($propsRelation, function ($prop) {
          $args = $prop->getAttributes(Id::class);
          return !empty($args);
        });


        $pkName = $pk[0]->getName();
        $method = "get" . ucfirst($pkName);

        $props[] = $name;
        $vals[":$name"] = $val->$method();
      }

      foreach ($this->relationsManyToMany as $relation) {
        $propName = $relation->getName();
        $method = "get" . ucfirst($propName);
        $val = $this->$method();
        $val->save();

        $reflectRelation = new \ReflectionClass($this->$propName);
        $propsRelation = $reflectRelation->getProperties();
        $pk = array_filter($propsRelation, function ($prop) {
          $args = $prop->getAttributes(Id::class);
          return !empty($args);
        });

        $pkName = $pk[0]->getName();
        $method = "get" . ucfirst($pkName);

        $relationId = $val->$method();

        $selfReflect = new \ReflectionClass($this);
        $propsRelation = $selfReflect->getProperties();
        $pk = array_filter($propsRelation, function ($prop) {
          $args = $prop->getAttributes(Id::class);
          return !empty($args);
        });


        $pkName = $pk[0]->getName();
        $method = "get" . ucfirst($pkName);

        $selfId = $this->$method();


        $attrs = $selfReflect->getAttributes(ManyToMany::class);
        $attr = $attrs[0]->newInstance();
        $assocTable = "";

        if ($attr->mainEntity === self::class) {
          $assocTable = "assoc_" . $selfReflect->getShortName() . "_" . $relation->name;
        } else {
          $assocTable = "assoc_" . $relation->name . "_" . $selfReflect->getShortName();
        }


        $sql = "
        IF NOT EXISTS (SELECT * FROM $assocTable WHERE id_" . $relation->name . " = :relationId AND id_" . $selfReflect->getShortName() . " = :selfId)
        BEGIN
          INSERT INTO $assocTable (id_" . $relation->name . ", id_" . $reflectRelation->getShortName() . ") VALUES (:relationId, :selfId)
        END";

        try {

          $stmt = $db->prepare($sql);
          $stmt->bindValue("selfId", $selfId, \PDO::PARAM_INT);
          $stmt->bindValue("relationId", $relationId, \PDO::PARAM_INT);
          $stmt->execute();
        } catch (\PDOException $e) {
          $db->rollBack();
          die($e->getMessage());
        }
      }


      $sql = "INSERT INTO " . $this->TABLE_NAME . " (id_utilisateur, id_type, id_priority, id_state, " . join(", ", $props) . ") VALUES (1, 1, 1, 1, " . join(", ", array_keys($vals)) . ")";

      try {

        // $stmt = $db->prepare($sql);

        // foreach($vals as $key => $val) {
        //   $stmt->bindValue($key, $val);
        // }

        echo '<pre>';
        echo $sql;
        // $stmt->execute();
        echo '</pre>';
      } catch (\PDOException $e) {
        $db->rollBack();
        die($e->getMessage());
      }


      $db->commit();
    }
  }





  public function hydrate(array $data, array $circularData = null): void {
    //tmp
    $r = new \ReflectionClass($this);
    $n = $r->getShortName();
    echo "hydrating $n<br>";
    //tmp
    $reflect = new \ReflectionClass($this);
    $props = $reflect->getProperties();

    foreach ($data as $key => $val) {
      if (str_starts_with($key, "id_")) {
        //relation
        $name = substr($key, offset: 3);

        echo "searching for prop $name<br>";
        $prop = array_filter($props, function ($prop) use ($name) {
          echo " prop name: " . $prop->getName() . "<br>";
          return $prop->getName() === $name;
        });
        $prop = array_pop($prop);

        $attrs = array_filter($prop->getAttributes(), function ($attr) {
          echo "attr name: " . $attr->getName() . "<br>";
          return $attr->getName() !== ManyToMany::class;
        });

        if (count($attrs) === 0) {
          continue;
        }



        $className = "Ticketing\\Repositories\\" . ucfirst($name) . "Manager";
        $method = "get" . ucfirst($name);
        $manager = new $className();
        $object = $manager->$method($val);

        $method = "set" . ucfirst($name);
        $this->$method($object);
      } else {
        $key = join(array_map(function ($a) {
          return ucfirst($a);
        }, explode("_", $key)));
        $method = "set" . ucfirst($key);

        $reflecProp = new ReflectionProperty($this, lcfirst($key));
        $attr = $reflecProp->getAttributes(Column::class);
        if (empty($attr)) {
          $this->$method($val);
          continue;
        }
        $attr = $attr[0]->newInstance();

        $val = match ($attr->type) {
          ColumnType::VARCHAR => (string) $val,
          ColumnType::INTEGER => (int) $val,
          ColumnType::BOOLEAN => (bool) $val,
          ColumnType::DATETIME => new \DateTime($val),
          ColumnType::DECIMAL => (float) $val,
          default => $val
        };

        $this->$method($val);
      }
    }


    foreach ($props as $mainProp) {
      echo "checking prop " . $mainProp->getName() . "<br>";
      $manies = $mainProp->getAttributes(ManyToMany::class);
      if (empty($manies)) {
        continue;
      }
      $relation = $manies[0];
      $attr = $relation->newInstance();
      $relationReflect = new \ReflectionClass($attr->targetEntity);

      if ($attr->mainEntity === $reflect->getName()) {
        $tableName = "assoc_" . $reflect->getShortName() . "_" . $relationReflect->getShortName();
      } else {
        $tableName = "assoc_" . $relationReflect->getShortName() . "_" . $reflect->getShortName();
      }

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

      $sql = "SELECT relation.* FROM " . $relationReflect->getShortName() . " relation JOIN $tableName assocTable ON relation.id = assocTable.id_" . $relationReflect->getShortName() . " WHERE assocTable.id_" . $reflect->getShortName() . " = :id";
      $stmt = Manager::getInstance()->prepare($sql);
      $stmt->bindValue("id", $id);
      $stmt->execute();

      echo "executing $sql<br>";

      $relations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
      $hydratedRelations = [];
      foreach ($relations as $relation) {

        $propName = $mainProp->getName();
        echo "main prop name: " . $propName . "<br>";

        $method = "set" . ucfirst($propName);
        echo "hydrating many to many relation $propName from $n<br>";

        if ($circularData !== null) {
          echo "circular data found<br>";
          foreach ($circularData as $data) {
            
          }
          // $this->$method($circularData);
        } else {
          echo "no circular data found<br>";
          $circularSql = "SELECT main.* FROM " . $reflect->getShortName() . " main JOIN $tableName assocTable ON main.id = assocTable.id_" . $reflect->getShortName() . " WHERE assocTable.id_" . $relationReflect->getShortName() . " = :id";
          $stmt = Manager::getInstance()->prepare($circularSql);
          $stmt->bindValue("id", $id);
          $stmt->execute();

          $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

          $hydratedData = [];

          foreach ($data as $row) {
            $className = $reflect->getName();
            echo "hydrating $className with circular data<br>";
            $relationClass = new $className();
            $relationClass->hydrate($row, []);
            $hydratedData[] = $relationClass;
          }


          $className = $relationReflect->getName();
          echo "hydrating $className with final circular data<br>";
          $relationClass = new $className();
          $relationClass->hydrate($relation, $hydratedData);
          echo "main prop name: " . $propName . "<br>";

          //find the setter method for the relation
          $setCircularMethod = "";
          $props = $relationReflect->getProperties();
          foreach ($props as $prop) {
            $attrs = $prop->getAttributes(ManyToMany::class);
            if (!empty($attrs)) {
              $attr = $attrs[0]->newInstance();
              if ($attr->targetEntity === $reflect->getName()) {
                $setCircularMethod = "set" . ucfirst($prop->getName());
              }
            }
          }
            
          $relationClass->$setCircularMethod($hydratedData);

          $hydratedRelations[] = $relationClass;
          
          $this->$method($hydratedRelations);
        }
      }
    }
  }
}
