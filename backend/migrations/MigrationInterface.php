<?php
namespace Migrations;

interface MigrationInterface
{
    public function up(\PDO $pdo): void;
    public function down(\PDO $pdo): void;
}