<h3><?= $msg ?></h3>
<?php
if ($authuser['id'] === $bidinfo->user_id) :
    //case buyer
    if (empty($bidinfo->buyer_name) || $bidinfo->errors()) :
        //フォーム未入力
        //発送先情報入力フォームの表示
        echo $this->Form->create($bidinfo, ['type' => 'post']);
        echo $this->Form->input('buyer_name');
        echo $this->Form->input('buyer_address');
        echo $this->Form->input('buyer_phone_number');
        echo $this->Form->button(__('Submit'));
        echo $this->Form->end();
    elseif ($bidinfo->is_sent and !$bidinfo->is_received) :
        //受取連絡ボタン表示
        echo $this->Form->create();
        echo $this->Form->hidden('is_received');
        echo $this->Form->button(__('受取連絡'));
    endif;
elseif ($authuser['id'] === $bidinfo->biditem->user_id) :
    //case seller
    if (!empty($bidinfo->buyer_name) and !$bidinfo->is_sent) :
        //発送連絡ボタン表示
        echo $this->Form->create();
        echo $this->Form->hidden('is_sent');
        echo $this->Form->button(__('送付連絡'));
    endif;
else :
    return $this->redirect(['controller' => 'Auction', 'action' => 'index']);
endif;
?>
