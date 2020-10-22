<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bidinfo Model
 *
 * @property \App\Model\Table\BiditemsTable&\Cake\ORM\Association\BelongsTo $Biditems
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\BidmessagesTable&\Cake\ORM\Association\HasMany $Bidmessages
 *
 * @method \App\Model\Entity\Bidinfo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Bidinfo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Bidinfo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Bidinfo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bidinfo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bidinfo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Bidinfo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Bidinfo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BidinfoTable extends Table
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

        $this->setTable('bidinfo');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Biditems', [
            'foreignKey' => 'biditem_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Bidmessages', [
            'foreignKey' => 'bidinfo_id',
        ]);
        $this->hasMany('Ratings', [
            'foreignKey' => 'bidinfo_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

        $validator
            ->scalar('buyer_name')
            ->allowEmptyString('buer_name', 'create')
            ->notEmptyString('buyer_name', 'update')
            ->requirePresence('buyer_name', 'update')
            ->maxLength('buyer_name', 255);

        $validator
            ->scalar('buyer_address')
            ->allowEmptyString('buyer_address', 'create')
            ->notEmptyString('buyer_address', 'update')
            ->requirePresence('buyer_address', 'update')
            ->maxLength('buyer_address', 255);

        $validator
            ->scalar('buyer_phone_number')
            ->allowEmptyString('buyer_phone_number', 'create')
            ->notEmptyString('buyer_phone_number', 'update')
            ->requirePresence('buyer_phone_number', 'update')
            ->regex('buyer_phone_number', '/^[0][0-9\-]{10,12}/', '半角数字とハイフンのみで入力してください')
            ->minLength('buyer_phone_number', 10, '半角数字とハイフンのみで10文字以上13文字以内で入力してください')
            ->maxLength('buyer_phone_number', 13, '半角数字とハイフンのみで10文字以上13文字以内で入力してください');

        $validator
            ->boolean('is_sent')
            ->requirePresence('is_sent', 'create')
            ->notEmptyString('is_sent', 'create');

        $validator
            ->boolean('is_received')
            ->requirePresence('is_recieved', 'create')
            ->notEmptyString('is_received', 'create');

        $validator
            ->boolean('is_seller_rated')
            ->requirePresence('is_seller_rated', 'create')
            ->notEmptyString('is_seller_rated', 'create');

        $validator
            ->boolean('is_buyer_rated')
            ->requirePresence('is_buyer_rated', 'create')
            ->notEmptyString('is_buyer_rated', 'create');

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
        $rules->add($rules->existsIn(['biditem_id'], 'Biditems'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
