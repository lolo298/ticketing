<?php

namespace ORM;

use Runtime\BDD;

use \Attribute;
use ReflectionProperty;

class Entity {
  private string $TABLE_NAME = "";
  /** @var ReflectionProperty[] */
  private array $columns = [];

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

    $this->columns = $cols;
  }

  public function save(): void {
    $db = BDD::getInstance();

    $id = array_filter($this->columns, function ($col) {
      $args = $col->getAttributes(Id::class);
      return !empty($args);
    });


    //checks nullable
    foreach($this->columns as $col) {
      $attrs = $col->getAttributes(Column::class);
      $colAttr = $attrs[0]->newInstance();

      if ($colAttr->default === false && $colAttr->nullable === false && $col->isInitialized($this) === false) {
        throw new \Exception("Column " . $col->getName() . " cannot be null");
      }
    }


    if (!empty($id) && $id[0]->isInitialized($this)) {
      //update
    } else {
      //insert
      // $props = join(', ', array_map(function ($prop) {
      //   return strtolower($prop->getName());
      // }, $this->columns));
      // $vals = join(', ', array_map(function ($prop) {
      //   return ":" . strtolower($prop->getName());
      // }, $this->columns));


      $props = [];
      $vals = [];

      foreach($this->columns as $col) {
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



      $sql = "INSERT INTO " . $this->TABLE_NAME . " (id_utilisateur, id_type, id_priority, id_state, ". join(", ", $props) .") VALUES (1, 1, 1, 1, ". join(", ", array_keys($vals)) .")";
      $stmt = $db->prepare($sql);

      foreach($vals as $key => $val) {
        $stmt->bindValue($key, $val);
      }

      echo '<pre>';
      echo $sql;
      $stmt->execute();
      echo '</pre>';


    }


    // $stmt = $db->prepare("INSERT INTO :table (:props) VALUES (:vals) ON DUPLICATE KEY :update");
    // $stmt->bindValue(":table", $this->TABLE_NAME);
    // $props = join(', ', array_map(function ($prop) {
    //   $attrs = $prop->getAttributes(Column::class);
    //   $col = $attrs[0]->newInstance();
    //   if ($col->PK) {
    //     return "id_".strtolower($prop->getName());
    //   }

    //   return strtolower($prop->getName());
    // }, $this->columns));
    // $sql = "INSERT INTO ($props) VALUE (:vals)";

    // $stmt->bindValue(":props", $props);




  }
}
