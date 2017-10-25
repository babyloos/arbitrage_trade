<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ExchangesAccountTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ExchangesAccountTable Test Case
 */
class ExchangesAccountTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ExchangesAccountTable
     */
    public $ExchangesAccount;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.exchanges_account'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ExchangesAccount') ? [] : ['className' => 'App\Model\Table\ExchangesAccountTable'];
        $this->ExchangesAccount = TableRegistry::get('ExchangesAccount', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ExchangesAccount);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
