<div class="grid">
<?php
$rows = $cols = 10;
for($x=0;$x<$rows;$x++): ?>
    <div class="row">
    <?php for($y=0;$y<$cols;$y++): ?>
        <div class="cell">
            <span class="cellcontent"></span>
        </div>
    <?php endfor; ?>
    </div>
<?php endfor; ?>
</div>