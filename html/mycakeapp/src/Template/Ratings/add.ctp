<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rating $rating
 */
?>

<div class="ratings form large-9 medium-8 columns content">
    <?= $this->Form->create($rating) ?>
    <fieldset>
        <legend><?= __('Add Rating') ?></legend>
        <?php
        echo $this->Form->control('rating', [
            'options' => [
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5'
            ],
            'type' => 'select'
        ]);
        echo $this->Form->control('comment', [
            'type' => 'textarea'
        ]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
