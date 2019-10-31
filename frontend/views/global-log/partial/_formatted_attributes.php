<?php

/**
 * @var array $formattedAttributes
 */
?>

<?php if (empty($formattedAttributes['old'])): ?>
    <p>Created</p>
<?php else: ?>
    <p>Updated</p>
<?php endif; ?>
<table class="table table-bordered table-hover">
    <tbody>
    <tr>
        <th>Attribute</th>
        <th style="width: 40%;">Old Value</th>
        <th style="width: 40%;">New Value</th>
    </tr>
    </tbody>
    <tbody>
	<?php foreach ($formattedAttributes['new'] as $key => $attribute) : ?>
        <tr>
            <th>
				<?= $key ?>
            </th>
            <td style="width: 40%; word-break: break-word;">
                                        <span class="item-new">
                                            <?= $formattedAttributes['old'][$key] ?? '' ?>
                                        </span>
            </td>
            <td style="width: 40%; word-break: break-word;">
                                        <span class="item-old">
                                            <?= $attribute ?>
                                        </span>
            </td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>

