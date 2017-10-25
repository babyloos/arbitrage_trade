<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Value Model
 *
 * @method \App\Model\Entity\Value get($primaryKey, $options = [])
 * @method \App\Model\Entity\Value newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Value[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Value|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Value patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Value[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Value findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ValueTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('value');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->numeric('coincheck_bid')
            ->allowEmpty('coincheck_bid');

        $validator
            ->numeric('coincheck_ask')
            ->allowEmpty('coincheck_ask');

        $validator
            ->numeric('zaif_bid')
            ->allowEmpty('zaif_bid');

        $validator
            ->numeric('zaif_ask')
            ->allowEmpty('zaif_ask');

        return $validator;
    }
}
