<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Components;

/**
 * Extended migration with additional functionality and soft delete columns.
 */
abstract class Migration extends \yii\db\Migration
{
    /**
     * {@inheritDoc}
     */
    public function createTable($table, $columns, $options = null, $softDelete = true)
    {
        $columns['createdBy'] = $this->string(36)->defaultValue(null);
        $columns['createdAt'] = $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP');
        $columns['updatedAt'] = $this->timestamp()->defaultValue(null);
        $columns['deletedAt'] = $this->timestamp()->defaultValue(null);

        if ($this->getDb()->getDriverName() === 'mysql') {
            $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        parent::createTable($table, $columns, $options);

        if ($this->getDb()->getDriverName() !== 'mysql') {
            $this->execute("CREATE INDEX \"idx{$table}_deletedAt\" ON \"{$table}\"((\"deletedAt\" IS NULL))");
        } else {
            $this->createSimpleIndex($table, ['deletedAt']);
        }

        $this->createSimpleIndex($table, ['createdBy']);
        $this->createSimpleIndex($table, ['createdAt']);
        $this->createSimpleIndex($table, ['updatedAt']);
    }

    /**
     * Simple foreign key addition.
     */
    public function addSimpleForeignKey(string $table, array $columns, string $refTable, array $refColumns = ['ID'])
    {
        $this->addForeignKey(
            "fk{$table}_{$refTable}",
            $table,
            $columns,
            $refTable,
            $refColumns,
            'RESTRICT',
            'RESTRICT'
        );
    }

    /**
     * Simple index.
     */
    public function createSimpleIndex(string $table, array $columns, bool $unique = false): void
    {
        $this->createIndex(
            "idx{$table}_".implode('-', $columns),
            $table,
            $columns,
            $unique
        );
    }
}