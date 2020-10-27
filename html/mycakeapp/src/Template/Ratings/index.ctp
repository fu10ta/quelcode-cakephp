<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rating[]|\Cake\Collection\CollectionInterface $ratings
 */
?>
<div class="ratings index large-9 medium-8 columns content">
    <h3>Rating AVG : <?= $ratingAvg ?></h3>
    <h3><?= __('Ratings') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('bidinfo_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('target_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('scorer_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('rating') ?></th>
                <th scope="col"><?= $this->Paginator->sort('comment') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ratings as $rating) : ?>
                <tr>
                    <td><?= $this->Number->format($rating->id) ?></td>
                    <td><?= $this->Number->format($rating->bidinfo_id) ?></td>
                    <td><?= $this->Number->format($rating->target_id) ?></td>
                    <td><?= $this->Number->format($rating->scorer_id) ?></td>
                    <td><?= $this->Number->format($rating->rating) ?></td>
                    <td><?= h($rating->comment) ?></td>
                    <td><?= h($rating->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $rating->bidinfo_id]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
