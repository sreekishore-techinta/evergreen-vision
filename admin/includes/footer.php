<?php
// Asset path is relative from admin/*.php — always correct
$rel_assets = defined('_HEADER_INCLUDED') ? $rel_assets : 'assets';
?>
</div><!-- /page-content -->
</main><!-- /admin-main -->
</div><!-- /admin-shell -->

<script src="assets/js/admin.js"></script>
<?php if (!empty($extra_js)): ?>
<script><?= $extra_js ?></script>
<?php endif; ?>
</body>
</html>
