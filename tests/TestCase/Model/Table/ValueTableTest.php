<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ValueTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ValueTable Test Case
 */
class ValueTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ValueTable
     */
    public $Value;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.value'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Value') ? [] : ['className' => 'App\Model\Table\ValueTable'];
        $this->Value = TableRegistry::get('Value', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Value);

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
