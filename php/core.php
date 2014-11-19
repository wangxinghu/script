<?php
//file_put_contents(__DIR__ . '/test.tpl', "TEST");
//ob_start();
//include __DIR__ . '/test.tpl';
//file_put_contents(__DIR__ . '/cache.tpl', ob_get_clean());
//include __DIR__ . '/cache.tpl';
if ($argv[1] > 0) {
      while ($argv[1]--) file_put_contents('test.tpl', "<?php #".str_repeat('A', mt_rand(4000, 5000))." ?>\n", LOCK_EX);
} else {
      $p2 = popen("php core.php 100", "r");
        while (1) include 'test.tpl';
}
