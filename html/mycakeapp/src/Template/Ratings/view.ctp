<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rating $rating
 */
?>
<?php
if (isset($buyerRating) && isset($sellerRating)) :
?>

    <div class="ratings view large-9 medium-8 columns content">
        <h3>出品者への評価</h3>
        <table>
            <tr>
                <th>rating</td>
                <td><?= h($sellerRating->rating) ?></td>
            </tr>
            <tr>
                <th>comment</td>
                <td><?= h($sellerRating->comment) ?></td>
            </tr>
        </table>

        <h3>落札者への評価</h3>
        <table>
            <tr>
                <th>rating</th>
                <td><?= h($buyerRating->rating) ?></td>
            </tr>
            <tr>
                <th>comment</th>
                <td><?= h($buyerRating->comment) ?></td>
            </tr>
        </table>
    </div>

<?php else : ?>
    <h3>相手の評価をお待ちください</h3>
<?php endif; ?>
