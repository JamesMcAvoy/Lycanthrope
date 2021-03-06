<?php
namespace Lycanthrope;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Output\OutputInterface;
use Lycanthrope\Exception\ConfigurationException as ConfigException;

final class Config {

    /**
     * Game components folder
     * @var String
     */
    private static $folder = __DIR__ . '/../game/';

    /**
     * Initialise les principaux composants de l'application
     * @param void
     * @return void
     */
    public static function boot() {

        self::confInit();
        self::dbInit();

    }

    /**
     * Exécute le schéma SQL en remettant à zéro la BDD
     * @param Symfony\Component\Console\Output\OutputInterface
     * @return void
     * @throws Lycanthrope\Exception\ConfigurationException
     */
    public static function schema(OutputInterface $output) {

        $schema = self::$folder . LYC_SCHEMA;

        if(!file_exists($schema)) {
            throw new ConfigException('Schema file not found.');
        }

        try {
            foreach(Capsule::select('SHOW TABLES') as $table) {
                $table_array = get_object_vars($table);
                Capsule::schema()->drop($table_array[key($table_array)]);
            }

            require_once $schema;
        } catch(\Exception $e) {
            throw new ConfigException($e->getMessage());
        } finally {
            $output->writeln('The database has been reseted.');
        }

        $output->writeln('The schema has been executed.');

    }

    /**
     * Initialise les constantes de configuration
     * @param void
     * @return void
     * @throws Lycanthrope\Exception\ConfigurationException
     */
    private static function confInit() {

        if(!file_exists(self::$folder . 'configuration.ini')) {
            throw new ConfigException('Configuration file not found.');
        }

        $config = parse_ini_file(self::$folder . 'configuration.ini');

        if(empty($config)) {
            throw new ConfigException('Configuration file empty.');
        }

        array_walk(
            $config,
            function($value, $key) {
                define(strtoupper('lyc_'.$key), $value);
            }
        );

    }

    /**
     * Initialise la connexion à la BDD
     * @param void
     * @return void
     * @throws Lycanthrope\Exception\ConfigurationException
     */
    private static function dbInit() {

        try {
            $capsule = new Capsule;

            $capsule->addConnection([
                'driver'    => LYC_DB_DRIVER,
                'host'      => LYC_DB_HOST,
                'port'      => LYC_DB_PORT,
                'database'  => LYC_DB_NAME,
                'username'  => LYC_DB_USER,
                'password'  => LYC_DB_PASS,
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();
        } catch(\Exception $e) {
            throw new ConfigException($e->getMessage());
        }

    }

}
