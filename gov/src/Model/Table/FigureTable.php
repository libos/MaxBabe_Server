<?php
namespace App\Model\Table;

use App\Model\Entity\Figure;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Figure Model
 */
class FigureTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('figure');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->allowEmpty('name')
            ->allowEmpty('filename')
            ->allowEmpty('path')
            ->allowEmpty('md5')
            ->allowEmpty('weather')
            ->add('ge_hour', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('ge_hour')
            ->add('le_hour', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('le_hour')
            ->add('ge_week', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('ge_week')
            ->add('le_week', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('le_week')
            ->add('ge_month', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('ge_month')
            ->add('le_month', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('le_month');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }
}
