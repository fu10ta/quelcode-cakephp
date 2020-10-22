<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rating $rating
 */
?>

<div class="ratings view large-9 medium-8 columns content">
    <h3><?= h($rating->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Comment') ?></th>
            <td><?= h($rating->comment) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($rating->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Bidinfo Id') ?></th>
            <td><?= $this->Number->format($rating->bidinfo_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Target Id') ?></th>
            <td><?= $this->Number->format($rating->target_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Scorer Id') ?></th>
            <td><?= $this->Number->format($rating->scorer_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rating') ?></th>
            <td><?= $this->Number->format($rating->rating) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($rating->created) ?></td>
        </tr>
    </table>
</div>
