<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event; // added.
use Exception; // added.

class AuctionController extends AuctionBaseController
{
	// デフォルトテーブルを使わない
	public $useTable = false;

	// 初期化処理
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Paginator');
		// 必要なモデルをすべてロード
		$this->loadModel('Users');
		$this->loadModel('Biditems');
		$this->loadModel('Bidrequests');
		$this->loadModel('Bidinfo');
		$this->loadModel('Bidmessages');
		// ログインしているユーザー情報をauthuserに設定
		$this->set('authuser', $this->Auth->user());
		// レイアウトをauctionに変更
		$this->viewBuilder()->setLayout('auction');
	}

	// トップページ
	public function index()
	{
		// ページネーションでBiditemsを取得
		$auction = $this->paginate('Biditems', [
			'order' => ['endtime' => 'desc'],
			'limit' => 10
		]);
		$this->set(compact('auction'));
	}

	// 商品情報の表示
	public function view($id = null)
	{
		// $idのBiditemを取得
		$biditem = $this->Biditems->get($id, [
			'contain' => ['Users', 'Bidinfo', 'Bidinfo.Users']
		]);
		// オークション終了時の処理
		if ($biditem->endtime < new \DateTime('now') and $biditem->finished == 0) {
			// finishedを1に変更して保存
			$biditem->finished = 1;
			$this->Biditems->save($biditem);
			// Bidinfoを作成する
			$bidinfo = $this->Bidinfo->newEntity();
			// Bidinfoのbiditem_idに$idを設定
			$bidinfo->biditem_id = $id;
			// 最高金額のBidrequestを検索
			$bidrequest = $this->Bidrequests->find('all', [
				'conditions' => ['biditem_id' => $id],
				'contain' => ['Users'],
				'order' => ['price' => 'desc']
			])->first();
			// Bidrequestが得られた時の処理
			if (!empty($bidrequest)) {
				// Bidinfoの各種プロパティを設定して保存する
				$bidinfo->user_id = $bidrequest->user->id;
				$bidinfo->user = $bidrequest->user;
				$bidinfo->price = $bidrequest->price;
				$this->Bidinfo->save($bidinfo);
			}
			// Biditemのbidinfoに$bidinfoを設定
			$biditem->bidinfo = $bidinfo;
		}
		// Bidrequestsからbiditem_idが$idのものを取得
		$bidrequests = $this->Bidrequests->find('all', [
			'conditions' => ['biditem_id' => $id],
			'contain' => ['Users'],
			'order' => ['price' => 'desc']
		])->toArray();
		// オブジェクト類をテンプレート用に設定
		$this->set(compact('biditem', 'bidrequests'));
	}

	// 出品する処理
	public function add()
	{
		// Biditemインスタンスを用意
		$biditem = $this->Biditems->newEntity();
		// POST送信時の処理
		if ($this->request->is('post')) {
			// アップロードを許可する拡張子を用意
			$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
			// アップロードファイルの拡張子取得
			$fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			if (in_array(strtolower($fileExtension), $allowedExtensions, true)) {
				// $biditemにフォームの送信内容を反映
				$biditem = $this->Biditems->patchEntity($biditem, $this->request->getData());
				// tmp.拡張子で仮置き
				$biditem['image_path'] = "tmp." . $fileExtension;
				// $biditemを保存する
				if ($this->Biditems->save($biditem)) {
					// biditem idに名前を変更し拡張子は保持する
					$image_path = "{$biditem['id']}.{$fileExtension}";
					$biditem['image_path'] = $image_path;
					move_uploaded_file($_FILES['image']['tmp_name'], 'img/auction/' . $image_path);
					if ($this->Biditems->save($biditem)) {
						// 成功時のメッセージ
						$this->Flash->success(__('保存しました。'));
						// トップページ（index）に移動
						return $this->redirect(['action' => 'index']);
					}
				}
				// 失敗時のメッセージ
				$this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
			} else {
				$this->Flash->error(__('JPG / PNG / GIF形式でアップロードください'));
			}
		}
		// 値を保管
		$this->set(compact('biditem'));
	}

	// 入札の処理
	public function bid($biditem_id = null)
	{
		// 入札用のBidrequestインスタンスを用意
		$bidrequest = $this->Bidrequests->newEntity();
		// $bidrequestにbiditem_idとuser_idを設定
		$bidrequest->biditem_id = $biditem_id;
		$bidrequest->user_id = $this->Auth->user('id');
		// POST送信時の処理
		if ($this->request->is('post')) {
			// $bidrequestに送信フォームの内容を反映する
			$bidrequest = $this->Bidrequests->patchEntity($bidrequest, $this->request->getData());
			// Bidrequestを保存
			if ($this->Bidrequests->save($bidrequest)) {
				// 成功時のメッセージ
				$this->Flash->success(__('入札を送信しました。'));
				// トップページにリダイレクト
				return $this->redirect(['action' => 'view', $biditem_id]);
			}
			// 失敗時のメッセージ
			$this->Flash->error(__('入札に失敗しました。もう一度入力下さい。'));
		}
		// $biditem_idの$biditemを取得する
		$biditem = $this->Biditems->get($biditem_id);
		$this->set(compact('bidrequest', 'biditem'));
	}

	// 落札者とのメッセージ
	public function msg($bidinfo_id = null)
	{
		// Bidmessageを新たに用意
		$bidmsg = $this->Bidmessages->newEntity();
		// POST送信時の処理
		if ($this->request->is('post')) {
			// 送信されたフォームで$bidmsgを更新
			$bidmsg = $this->Bidmessages->patchEntity($bidmsg, $this->request->getData());
			// Bidmessageを保存
			if ($this->Bidmessages->save($bidmsg)) {
				$this->Flash->success(__('保存しました。'));
			} else {
				$this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
			}
		}
		try { // $bidinfo_idからBidinfoを取得する
			$bidinfo = $this->Bidinfo->get($bidinfo_id, ['contain' => ['Biditems']]);
		} catch (Exception $e) {
			$bidinfo = null;
		}
		// Bidmessageをbidinfo_idとuser_idで検索
		$bidmsgs = $this->Bidmessages->find('all', [
			'conditions' => ['bidinfo_id' => $bidinfo_id],
			'contain' => ['Users'],
			'order' => ['created' => 'desc']
		]);
		$this->set(compact('bidmsgs', 'bidinfo', 'bidmsg'));
	}

	// 落札情報の表示
	public function home()
	{
		// 自分が落札したBidinfoをページネーションで取得
		$bidinfo = $this->paginate('Bidinfo', [
			'conditions' => ['Bidinfo.user_id' => $this->Auth->user('id')],
			'contain' => ['Users', 'Biditems'],
			'order' => ['created' => 'desc'],
			'limit' => 10
		])->toArray();
		$this->set(compact('bidinfo'));
	}

	// 出品情報の表示
	public function home2()
	{
		// 自分が出品したBiditemをページネーションで取得
		$biditems = $this->paginate('Biditems', [
			'conditions' => ['Biditems.user_id' => $this->Auth->user('id')],
			'contain' => ['Users', 'Bidinfo'],
			'order' => ['created' => 'desc'],
			'limit' => 10
		])->toArray();
		$this->set(compact('biditems'));
	}

	//発送・受取連絡の表示
	public function personalInfo($bidinfo_id)
	{
		//取引情報の取得
		$bidinfo = $this->Bidinfo->get($bidinfo_id, [
			'contain' => ['Biditems']
		]);

		//フォームからの情報で取引情報を更新
		if ($this->request->is('post')) :
			if (isset($_POST['buyer_name'])) :
				$bidinfo->buyer_name = $_POST['buyer_name'];
				$bidinfo->buyer_address = $_POST['buyer_address'];
				$bidinfo->buyer_phone_number = $_POST['buyer_phone_number'];
			elseif (isset($_POST['is_sent'])) :
				$bidinfo->is_sent = 1;
			elseif (isset($_POST['is_received'])) :
				$bidinfo->is_received = 1;
			endif;
			if ($this->Bidinfo->save($bidinfo)) :
				$this->Flash->success(__('保存しました'));
			else :
				$this->Flash->error(__('保存に失敗しました。もう一度入力ください'));
			endif;
		endif;

		if ($this->Auth->user('id') === $bidinfo->user_id) :
			//case buyer
			if (empty($bidinfo->buyer_name)) :
				//フォーム未入力
				$msg = '発送先情報を入力してください';
			elseif (!$bidinfo->is_sent) :
				//発送連絡待ち
				$msg = '出品者による送付連絡をお待ちください';
			elseif (!$bidinfo->is_received) :
				//受取連絡待ち
				$msg = '商品受取後受取連絡ボタンを押してください';
			else :
				//評価画面への遷移
				$this->Auth->setUser($this->Auth->user());
				return $this->redirect(['controller' => 'Ratings', 'action' => 'add']);
			endif;
		elseif ($this->Auth->user('id') === $bidinfo->biditem->user_id) :
			//case seller
			if (empty($bidinfo->buyer_name)) :
				//フォーム未入力
				$msg = '落札者による発送先情報をおまちください';
			elseif (!$bidinfo->is_sent) :
				$msg = "商品発送後発送連絡ボタンを押してください<br>
						落札者名： $bidinfo->buyer_name <br>
						発送先住所： $bidinfo->buyer_address <br>
						落札者電話番号： $bidinfo->buyer_phone_number";
			elseif (!$bidinfo->is_received) :
				$msg = '落札者による受取確認をお待ちください';
			else :
				//評価画面への遷移
				$this->Auth->setUser($this->Auth->user());
				return $this->redirect(['controller' => 'Ratings', 'action' => 'add']);
			endif;
		else :
			return $this->redirect(['action' => 'index']);
		endif;
		$this->set(compact('bidinfo', 'msg'));
	}
}
