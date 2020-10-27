<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Exception;

/**
 * Ratings Controller
 *
 * @property \App\Model\Table\RatingsTable $Ratings
 *
 * @method \App\Model\Entity\Rating[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RatingsController extends AuctionBaseController
{
    public $useTable = false;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadModel('Ratings');
        $this->loadModel('Biditems');
        $this->loadModel('Bidinfo');
        $this->set('authuser', $this->Auth->user());
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $ratingAvg = $this->Ratings->find()->where(['target_id' => $this->Auth->user('id')])->avg('rating');
        $ratings = $this->paginate('Ratings', [
            'conditions' => ['Ratings.target_id' => $this->Auth->user('id')],
            'order' => ['created' => 'desc'],
            'limit' => 20
        ])->toArray();
        $this->set(compact('ratingAvg', 'ratings'));
    }

    /**
     * View method
     *
     * @param string|null $id Rating id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($bidinfo_id)
    {
        $bidinfo = $this->Bidinfo->get($bidinfo_id, [
            'contain' => ['Biditems'],
        ]);

        //ログインユーザーの認証
        if ($this->Auth->user('id') !== $bidinfo->user_id && $this->Auth->user('id') !== $bidinfo->biditem->user_id) {
            return $this->redirect(['controller' => 'Auction', 'action' => 'index']);
        }

        //取引途中のリダイレクト
        if (!isset($bidinfo->buyer_name) || !$bidinfo->is_sent || !$bidinfo->is_received) {
            return $this->redirect(['controller' => 'Auction', 'action' => 'personalInfo', $bidinfo_id]);
        }

        if ($this->Auth->user('id') === $bidinfo->user_id && !$bidinfo->is_buyer_rated) :
            //case buyer && !rated
            return $this->redirect(['action' => 'add', $bidinfo->id]);
        elseif ($this->Auth->user('id') === $bidinfo->biditem->user_id && !$bidinfo->is_seller_rated) :
            //case seller && !rated
            return $this->redirect(['action' => 'add', $bidinfo->id]);
        else :
            $buyerRatings = $this->Ratings->find('all', [
                'conditions' => ['bidinfo_id' => $bidinfo->id, 'target_id' => $bidinfo->user_id]
            ]);
            $sellerRatings = $this->Ratings->find('all', [
                'conditions' => ['bidinfo_id' => $bidinfo->id, 'target_id' => $bidinfo->biditem->user_id]
            ]);
            $buyerRating = $buyerRatings->first();
            $sellerRating = $sellerRatings->first();
            $this->set(compact('buyerRating', 'sellerRating'));
        endif;
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */

    public function add($bidinfo_id)
    {

        $bidinfo = $this->Bidinfo->get($bidinfo_id, [
            'contain' => ['Biditems'],
        ]);

        //ログインユーザーの認証
        if ($this->Auth->user('id') !== $bidinfo->user_id && $this->Auth->user('id') !== $bidinfo->biditem->user_id) {
            return $this->redirect(['controller' => 'Auction', 'action' => 'index']);
        }

        //取引途中のリダイレクト
        if (!isset($bidinfo->buyer_name) || !$bidinfo->is_sent || !$bidinfo->is_received) {
            return $this->redirect(['controller' => 'Auction', 'action' => 'personalInfo', $bidinfo->id]);
        }

        $rating = $this->Ratings->newEntity();

        //評価状況に応じて条件分岐
        if ($bidinfo->is_seller_rated && $bidinfo->is_buyer_rated) :
            //双方評価済
            return $this->redirect(['action' => 'view', $bidinfo_id]);
        elseif ($this->request->is('post')) :
            //フォームからの遷移
            if ($this->Auth->user('id') === $bidinfo->user_id) :
                //case buyer
                $ratingPatchData['target_id'] = $bidinfo->biditem->user_id;
                $bidinfo->is_buyer_rated = 1;
            else :
                //case seller
                $ratingPatchData['target_id'] = $bidinfo->user_id;
                $bidinfo->is_seller_rated = 1;
            endif;
            $ratingPatchData['bidinfo_id'] = $bidinfo_id;
            $ratingPatchData['scorer_id'] = $this->Auth->user('id');
            $ratingPatchData['rating'] = $_POST['rating'];
            $ratingPatchData['comment'] = $_POST['comment'];

            //入力された情報をRatingに、評価状況をbidinfoに反映
            $rating = $this->Ratings->patchEntity($rating, $ratingPatchData);
            if ($this->Ratings->save($rating) && $this->Bidinfo->save($bidinfo)) {
                $this->Flash->success(__('保存しました'));
                return $this->redirect(['action' => 'view', $bidinfo_id]);
            } else {
                $this->Flash->error(__('保存に失敗しました。もう一度入力ください。'));
            }
        endif;
        $this->set(compact('bidinfo_id', 'rating'));
    }
}
