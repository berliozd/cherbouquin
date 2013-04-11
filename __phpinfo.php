<?php
highlight_file(__FILE__);
echo 'Avant : ' . number_format(memory_get_usage(), 0, '.', ',') . " octets\n";
phpinfo(); 
?>
