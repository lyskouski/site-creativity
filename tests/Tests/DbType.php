<?php namespace Tests;

/**
 * Basic functionality for tests with DB
 *
 * @author Viachaslau Lyskouski
 */
class DbType extends Simple
{

    protected function setUp()
    {
        parent::setUp();
        $this->init();
        $this->fillByXML(__DIR__ . '/Config/start.xml');
    }

    public function fillByXML($file)
    {
        $em = \System\Registry::connection();
        $connection = new \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($em);
        $filler = new \PHPUnit_Extensions_Database_DefaultTester($connection);
        $filler->setDataSet(new \PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet($file));
        $filler->onSetUp();
    }

    /**
     * Add necessary fields for an initial run
     *
     * @param string $file
     */
    public function fillBySQL($file)
    {
        \System\Registry::connection()->getConnection()
                ->exec(file_get_contents($file));
    }

    protected function tearDown()
    {
        parent::tearDown();
        \System\Registry::get(Config\Database::REG_TYPE)
                ->revalidate(\System\Registry::connection());
    }

}
