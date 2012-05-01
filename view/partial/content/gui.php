<div class="grid">
<?php
$rows = $cols = 10;
for($x=0;$x<$rows;$x++): ?>
    <div class="row">
    <?php for($y=0;$y<$cols;$y++): ?>
        <div class="cell">
			<div class="cellsection no"></div>
			<div class="cellsection ne"></div>
            <div class="cellsection so"></div>
			<div class="cellsection se"></div>
        </div>
    <?php endfor; ?>
    </div>
<?php endfor; ?>
</div>