<?php
namespace Core;

use PDO;

/**
 * Klasse: Database
 * Verwaltet eine einzelne SQLite-Datenbankverbindung (Singleton).
 * Diese Klasse stellt sicher, dass in der gesamten Anwendung
 * nur eine einzige PDO-Verbindung erstellt und wiederverwendet wird.
 * Sie dient als zentrale Datenquelle für alle Repository-Klassen.
 *
 * Hauptfunktionen:
 * - Erzeugt automatisch eine SQLite-Verbindung (Dateibasierte DB)
 * - Aktiviert den Exception-Modus für PDO-Fehlerbehandlung
 * - Verhindert Mehrfachinstanziierung durch privaten Konstruktor
 *
 * @package Core
 */
class Database
{
    /**
     * @var PDO|null  Singleton-Instanz der PDO-Verbindung
     */
    private static ?PDO $connection = null;

    /**
     * Gibt eine Singleton-Instanz der PDO-Verbindung zurück.
     *
     * Wenn noch keine Verbindung existiert, wird automatisch eine neue
     * SQLite-Datenbank im Verzeichnis `/db/todos.db` erstellt.
     *
     * @return PDO  Eine aktive SQLite-Datenbankverbindung
     * @throws \PDOException  Wenn der Verbindungsaufbau fehlschlägt
     */
    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            $path = __DIR__ . '/../db/todos.db';
            $dsn = 'sqlite:' . $path;

            // Verbindung zur SQLite-Datei herstellen
            self::$connection = new PDO($dsn);

            // Fehlerbehandlung: PDO wirft Exceptions bei Fehlern
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$connection;
    }

    /**
     * Privater Konstruktor verhindert direkte Instanziierung.
     *
     * Die Klasse wird ausschließlich über {@see Database::getConnection()} verwendet.
     */
    private function __construct() {}
}