<?php

namespace Group\Dao;

use Group\Dao\ExtendedPdo;
use Group\Dao\ConnectionLocator;

class Dao
{
    protected $config;

    private static $connection;

    public function __construct()
    {
        $pdo = \Config::get('database::pdo');
        $this->config = $pdo;
    }

    /**
     * 获取默认服务器连接
     *
     * @return object
     */
    public function getDefault()
    {
        return $this->logger($this->getConnection()->getDefault());
    }

    /**
     * 获取某个读服务器连接
     *
     * @param  name
     * @return object
     */
    public function getRead($name = null)
    {
        return $this->logger($this->getConnection()->getRead($name));
    }

    /**
     * 获取某个写服务器连接
     *
     * @param  name
     * @return object
     */
    public function getWrite($name = null)
    {
        return $this->logger($this->getConnection()->getWrite($name));
    }

    /**
     * 获取所有读服务器的连接
     *
     * @return array
     */
    public function getAllRead()
    {
        $config = $this->config;
        $connections = [];

        if (isset($config['read'])) {
            foreach ($config['read'] as $name => $db) {
                $connections[] = $this->getRead($name);
            }
        }

        return $connections;
    }

    /**
     * 获取所有写服务器的连接
     *
     * @return array
     */
    public function getAllWrite()
    {
        $config = $this->config;
        $connections = [];

        if (isset($config['write'])) {
            foreach ($config['write'] as $name => $db) {
                $connections[] = $this->getWrite($name);
            }
        }

        return $connections;
    }

    /**
     * 执行sql
     *
     * @param  sql
     * @param  type[write|all_write|read|all_read|default]
     * @param  name
     */
    public function querySql($sql, $type, $name = null)
    {
        switch ($type) {
            case 'write':
                    return $this->getWrite($name)->query($sql);
                break;
            case 'all_write':
                    $connections = $this->getAllWrite();
                    foreach ($connections as $connection) {
                        $connection->query($sql);
                    }
                break;
            case 'read':
                    return $this->getRead($name)->query($sql);
                break;
            case 'all_read':
                    $connections = $this->getAllRead();
                    foreach ($connections as $connection) {
                        $connection->query($sql);
                    }
                break;
            case 'default':
                    return $this->getDefault()->query($sql);
                break;
            default:
                break;
        }
    }

    protected function getConnection()
    {
        if (self::$connection) {
            return self::$connection;
        }

        $connection = $this->getConnectionLocator();

        self::$connection = $connection;

        return $connection;
    }

    private function getConnectionLocator()
    {
        $config = $this->config;
        $connections = new ConnectionLocator;

        if (isset($config['default'])) {
            $connections->setDefault(function () use ($config) {
                return \Doctrine\DBAL\DriverManager::getConnection($config['default'], new \Doctrine\DBAL\Configuration());
            });
        }

        if (isset($config['write'])) {
            foreach ($config['write'] as $name => $db) {
                $connections->setWrite($name, function () use ($db) {
                    return \Doctrine\DBAL\DriverManager::getConnection($db, new \Doctrine\DBAL\Configuration());
                });
            }
        }

        if (isset($config['read'])) {
            foreach ($config['read'] as $name => $db) {
                $connections->setRead($name, function () use ($db) {
                    return \Doctrine\DBAL\DriverManager::getConnection($db, new \Doctrine\DBAL\Configuration());
                });
            }
        }

        return $connections;
    }

    private function logger($connection)
    {   
        if (app('container')->isDebug() && !app('container') ->runningInConsole()) {
            $connection->getConfiguration()->setSQLLogger(app('debugStack'));
        }

        if ($connection->ping() === false) {
           $connection->close();
           $connection->connect();
        }

        return $connection;
    }
}
