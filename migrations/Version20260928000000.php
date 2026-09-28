<?php

namespace DbMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial database schema for Eve Moon Scan Analyzer.
 */
final class Version20260928000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial schema for moons, materials, and scans';
    }

    public function up(Schema $schema): void
    {
        // Moon table
        $moons = $schema->createTable('moons');
        $moons->addColumn('eveId', 'integer', ['unsigned' => true]);
        $moons->addColumn('name', 'string', ['length' => 255, 'notnull' => false]);
        $moons->addColumn('systemId', 'integer', ['unsigned' => true, 'notnull' => false]);
        $moons->addColumn('systemName', 'string', ['length' => 255, 'notnull' => false]);
        $moons->addColumn('planetName', 'string', ['length' => 255, 'notnull' => false]);
        $moons->addColumn('moonIndex', 'string', ['length' => 255, 'notnull' => false]);
        $moons->addColumn('ownedBy', 'string', ['length' => 255, 'notnull' => false]);
        $moons->addColumn('notes', 'string', ['length' => 500, 'notnull' => false]);
        $moons->addColumn('createdAt', 'datetime');
        $moons->addColumn('updatedAt', 'datetime');
        $moons->addColumn('lastScannedAt', 'datetime', ['notnull' => false]);
        $moons->setPrimaryKey(['eveId']);
        $moons->addIndex(['systemId'], 'idx_system_id');
        $moons->addIndex(['lastScannedAt'], 'idx_last_scanned');

        // Material table
        $materials = $schema->createTable('materials');
        $materials->addColumn('id', 'integer', ['autoincrement' => true]);
        $materials->addColumn('eveTypeId', 'integer');
        $materials->addColumn('name', 'string', ['length' => 255]);
        $materials->addColumn('category', 'string', ['length' => 50, 'notnull' => false]);
        $materials->addColumn('currentPrice', 'float', ['notnull' => false]);
        $materials->addColumn('priceUpdatedAt', 'datetime', ['notnull' => false]);
        $materials->addColumn('createdAt', 'datetime');
        $materials->addColumn('updatedAt', 'datetime');
        $materials->setPrimaryKey(['id']);
        $materials->addUniqueIndex(['eveTypeId']);
        $materials->addIndex(['name'], 'idx_material_name');

        // MoonMaterial table (junction)
        $moonMaterials = $schema->createTable('moon_materials');
        $moonMaterials->addColumn('id', 'integer', ['autoincrement' => true]);
        $moonMaterials->addColumn('moon_id', 'integer', ['unsigned' => true]);
        $moonMaterials->addColumn('material_id', 'integer');
        $moonMaterials->addColumn('quantity', 'float');
        $moonMaterials->addColumn('totalValue', 'float', ['notnull' => false]);
        $moonMaterials->addColumn('scannedAt', 'datetime');
        $moonMaterials->setPrimaryKey(['id']);
        $moonMaterials->addUniqueIndex(['moon_id', 'material_id'], 'unique_moon_material');
        $moonMaterials->addForeignKeyConstraint('moons', ['moon_id'], ['eveId'], [], 'fk_moon_id');
        $moonMaterials->addForeignKeyConstraint('materials', ['material_id'], ['id'], [], 'fk_material_id');
        $moonMaterials->addIndex(['scannedAt'], 'idx_scanned_at');

        // Scan table
        $scans = $schema->createTable('scans');
        $scans->addColumn('id', 'integer', ['autoincrement' => true]);
        $scans->addColumn('scanType', 'string', ['length' => 50]);
        $scans->addColumn('rawData', 'text');
        $scans->addColumn('moonCount', 'integer');
        $scans->addColumn('materialCount', 'integer');
        $scans->addColumn('submittedBy', 'string', ['length' => 255, 'notnull' => false]);
        $scans->addColumn('status', 'string', ['length' => 50, 'notnull' => false]);
        $scans->addColumn('errorMessage', 'text', ['notnull' => false]);
        $scans->addColumn('createdAt', 'datetime');
        $scans->setPrimaryKey(['id']);
        $scans->addIndex(['createdAt'], 'idx_created_at');
        $scans->addIndex(['submittedBy'], 'idx_submitted_by');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('moon_materials');
        $schema->dropTable('scans');
        $schema->dropTable('materials');
        $schema->dropTable('moons');
    }
}
