<?php

namespace svit\tools\db;

class Migration extends \yii\db\Migration
{
    const FK_RESTRICT = 'RESTRICT';
    const FK_CASCADE = 'CASCADE';
    const FK_NO_ACTION = 'NO ACTION';
    const FK_SET_DEFAULT = 'SET DEFAULT';
    const FK_SET_NULL = 'SET NULL';

    public $character = 'utf8mb4';
    public $collate = 'utf8mb4_unicode_ci';

    public function createTable($table, $columns, $options = null)
    {
        if ($options === null && $this->db->driverName === 'mysql') {
            $options = "CHARACTER SET {$this->character} COLLATE {$this->collate}";
        }

        parent::createTable($table, $columns, $options);
    }

    public function notExistTable($name)
    {
        return $this->db->getTableSchema($name, true) === null;
    }

    public function addPK($table, $columns)
    {
        $name = $this->generateName('pk', $table);

        parent::addPrimaryKey($name, $table, $columns);
    }

    public function addFK($table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        $name = $this->generateName('fk', $table, $refTable);

        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    public function dropFK($table, $refTable)
    {
        $name = $this->generateName('fk', $table, $refTable);

        parent::dropForeignKey($name, $table);
    }

    public function addIdx($table, $columns, $unique = false)
    {
        $name = $this->generateName('idx', $table, ...(array)$columns);

        parent::createIndex($name, $table, $columns, $unique);
    }

    public function dropIdx($table, $columns)
    {
        $name = $this->generateName('idx', $table, ...(array)$columns);

        parent::dropIndex($name, $table);
    }

    public function generateName(...$args)
    {
        return implode('-', $args);
    }
}
