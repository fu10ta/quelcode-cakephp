<h3><?= $msg ?></h3>
<?php
if ($authuser['id'] === $bidinfo->user_id) :
    //case buyer
    if (empty($bidinfo->buyer_name)) :
        //フォーム未入力
        //発送先情報入力フォームの表示
        echo $this->Form->create();
        echo $this->Form->hidden('bidinfo_id', ['value' => $bidinfo->id]);
        echo $this->Form->hidden('user_id', ['value' => $authuser['id']]);
        echo $this->Form->control('buyer_name');
        echo $this->Form->control('buyer_address');
        echo $this->Form->control('buyer_phone_number');
        echo $this->Form->button(__('Submit'));
        echo $this->Form->end();
    elseif ($bidinfo->is_sent and !$bidinfo->is_received) :
        //受取連絡ボタン表示
        echo $this->Form->create();
        echo $this->Form->hidden('is_received', ['value' => $bidinfo['id']]);
        echo $this->Form->button(__('受取連絡'));
    endif;
elseif ($authuser['id'] === $bidinfo->biditem->user_id) :
    //case seller
    if (!empty($bidinfo->buyer_name) and !$bidinfo->is_sent) :
        //発送連絡ボタン表示
        echo $this->Form->create();
        echo $this->Form->hidden('is_sent', ['value' => $bidinfo['id']]);
        echo $this->Form->button(__('送付連絡'));
    endif;
else :
    return $this->redirect(['controller' => 'Auction', 'action' => 'index']);
endif;
?>
