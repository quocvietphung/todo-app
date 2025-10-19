<?php
namespace Core;

use ReflectionClass;
use ReflectionParameter;
use PDO;

/**
 * Klasse: Container
 * ---------------------------------------------------------
 * Ein leightgewichtiger Dependency-Injection-Container (Singleton)
 * Diese Klasse erstellt und verwaltet automatisch Instanzen von Klassen
 * über Reflection. Abhängigkeiten werden rekursiv aufgelöst und injiziert
 */
class Container
{
    /**
     * @var self|null  Singleton-Instanz des Containers
     */
    private static ?self $instance = null;
    /**
     * @var array  Cache für bereits erzeugte Objekte
     */
    private array $instances = [];

    private function __construct() {}

    /** 🔁 Singleton accessor */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Gibt eine Instanz der angegebenen Klasse zurück und injiziert deren Abhängigkeiten automatisch.
     *
     * Wenn eine Instanz bereits existiert, wird sie aus dem Cache zurückgegeben.
     * Falls die Klasse einen PDO-Typ erwartet, wird automatisch eine SQLite-Verbindung aus
     * {@see Database::getConnection()} injiziert.
     *
     * @param string $class Vollqualifizierter Klassenname (z. B. Controller\TodoController)
     *
     * @return object Eine vollständig initialisierte Klasseninstanz
     * @throws \ReflectionException Wenn die Klasse nicht existiert
     */
    public function get(string $class)
    {
        // Sonderfall: Wenn PDO benötigt wird → Datenbankverbindung zurückgeben
        if ($class === PDO::class) {
            return Database::getConnection();
        }

        // Wenn Instanz bereits existiert → aus Cache verwenden
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        // Reflection: Konstruktor analysieren und Abhängigkeiten auflösen
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            // Klasse hat keinen Konstruktor → direkt instanziieren
            $object = new $class();
        } else {
            // Alle Parameter (Abhängigkeiten) auslesen und rekursiv injizieren
            $dependencies = array_map(function (ReflectionParameter $param) {
                $type = $param->getType();
                if ($type && !$type->isBuiltin()) {
                    return $this->get($type->getName());
                }
                return null;
            }, $constructor->getParameters());

            $dependencies = array_filter($dependencies); // Null-Werte entfernen
            $object = $reflection->newInstanceArgs($dependencies);
        }

        // Objekt im Cache speichern (lokales Singleton)
        return $this->instances[$class] = $object;
    }
}