<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AssetTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AssetTable Test Case
 */
class AssetTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\AssetTable
     */
    public $Asset;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.asset'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Asset') ? [] : ['className' => 'App\Model\Table\AssetTable'];
        $this->Asset = TableRegistry::get('Asset', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Asset);

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
