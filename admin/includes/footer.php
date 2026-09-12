<?php
// $assets_url is set by header.php — fallback just in case
$assets_url = defined('ADMIN_URL') ? ADMIN_URL . '/assets' : '/evergreen-vision/admin/assets';
?>
</div><!-- /page-content -->
</main><!-- /admin-main -->
</div><!-- /admin-shell -->

<script src="<?= $assets_url ?>/js/admin.js"></script>
<?php if (!empty($extra_js)): ?>
<script><?= $extra_js ?></script>
<?php endif; ?>
</body>
</html>
