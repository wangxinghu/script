<?php
function http($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    $head = curl_exec($ch);
    curl_close($ch);
    return $head;
}

//$url = 'http://ffs-dev-scene2.socialgamenet.com/mobilegateway.php?req={%22Target%22:%22MobileDataHandler.handle%22,%22data%22:[%22retrieve_data%22,{%22snsid%22:%2227329dec63ea6ad417966084525be8c8%22,%22login_session%22:%22C5A6003E-DF71-4B13-93E5-FEADA05F2FA3%22,%22udid%22:%22%22,%22farm_uuid%22:%22C5A6003E-DF71-4B13-93E5-FEADA05F2FA3%22,%22lang%22:%22en_US%22,%22resource_type%22:%22high%22,%22version%22:%223.0.0.0%22,%22platform%22:%22iOS%22,%22transport%22:%22http://ffs-dev-scene2.socialgamenet.com/%22,%22scene%22:1,%22product%22:%22ffs.dev.iOS%22,%22time_zone_offset%22:28800,%22gcid%22:%22%22,%22old_session%22:%22%22,%22session_device_id%22:%22%22,%22app_type%22:%22native%22,%22device_token%22:%22%22,%22system_version%22:%227.1%22,%22openUDID%22:%227fce48010e80e9c8bb2660977b2b490dce749e51%22,%22IDFA%22:%2272CE81A5-DC5D-49F2-8C5F-C0AFF4D424C5%22,%22device_type%22:%22ios%22,%22device_model%22:%22x86_64%22,%22mac_address%22:%22%22,%22mat_id%22:%22%22},%22retrieve%22]}';
$url = 'http://127.0.0.1:8080/mobilegateway.php?req={%22Target%22:%22MobileDataHandler.handle%22,%22data%22:[%22retrieve_data%22,{%22snsid%22:%227572b8357dc8e9ca6b8d6d3f461f3642%22,%22login_session%22:%2265C7161B-EA28-48D4-8A7F-A846319DE9FF%22,%22udid%22:%22%22,%22farm_uuid%22:%2265C7161B-EA28-48D4-8A7F-A846319DE9FF%22,%22lang%22:%22en_US%22,%22resource_type%22:%22high%22,%22version%22:%223.0.0.0%22,%22platform%22:%22iOS%22,%22transport%22:%22http://127.0.0.1:8080/%22,%22scene%22:1,%22product%22:%22ffs.dev.iOS%22,%22time_zone_offset%22:28800,%22gcid%22:%22%22,%22old_session%22:%22%22,%22session_device_id%22:%22%22,%22app_type%22:%22native%22,%22device_token%22:%22%22,%22system_version%22:%228.0%22,%22openUDID%22:%228757c94815c7e0dbd0b5a8d5c8ce766091abfcd9%22,%22IDFA%22:%2299D9D599-CD42-4840-AE2D-8547664C92A3%22,%22device_type%22:%22ios%22,%22device_model%22:%22x86_64%22,%22mac_address%22:%22%22,%22mat_id%22:%22%22},%22retrieve%22]}';
$i = 1000;
while ($i > 0 ) {
    $res = http($url);
    $i--;
}
