<?php
@unlink(__DIR__ . '/import_db.php');
@unlink(__DIR__ . '/unzip.php');
@unlink(__DIR__ . '/baski_deploy_linux.zip');
@unlink(__DIR__ . '/baski_deploy.zip');
@unlink(__FILE__);
echo json_encode(['cleaned' => true]);
