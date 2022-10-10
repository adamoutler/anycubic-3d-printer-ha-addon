<?php
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (gettype($haystack) === "array") {
        return substr($haystack[0], 0, $length) === $needle;
    }
    return substr($haystack, 0, $length) === $needle;
}
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}
$data = "gethistory,1/3DBenchy-heavy-antialias.pwmb/6.pwmb/22318669/223/1040/18/60.0/4.0,2/3DBenchy-heavy-antialias.pwmb/13.pwmb/22318669/220/1040/18/40.0/4.0,3/splenda packet holder.pwmb/45.pwmb/34772459/358/800/28/40.0/4.0,4/splenda packet holder.pwmb/53.pwmb/34772459/358/800/28/40.0/4.0,5/2 phone stands on side.pwmb/2.pwmb/135149364/772/1726/376/40.0/4.0,6/DANGER-173.pwmb/8.pwmb/79358167/330/719/52/40.0/4.0,7/DANGER-173-rev2.pwmb/9.pwmb/127765654/479/1057/99/40.0/4.0,8/WARNING_SIGN_v1.pwmb/10.pwmb/89290088/360/854/128/40.0/4.0,9/WARNING_SIGN_v1_rev3.pwmb/11.pwmb/53594049/210/440/132/40.0/5.0,10/splenda tray.pwmb/12.pwmb/61651790/402/860/139/45.0/5.0,11/SLA print puller supported.pwmb/1.pwmb/6416188/215/482/2/40.0/4.0,12/2 phone stands on side.pwmb/15.pwmb/135149364/518/1726/376/80.0/5.0,13/steampunk light switch and printer guard.pwmb/13.pwmb/39832673/169/581/123/60.0/5.0,14/Steam-Crank-Logo-Stand.pwmb/44.pwmb/14724078/194/693/19/60.0/5.0,15/2 phone stands on side.pwmb/47.pwmb/135149364/440/1726/376/60.0/5.0,16/2 phone stands on side.pwmb/77.pwmb/135149364/912/1726/376/90.0/4.0,17/Anycubi_Mono_X_6K_Build_plate_suspension.pwmb/232.pwmb/86831562/925/1152/94/90.0/5.0,18/ResinDrainer1000ml_Cat2.pwmb/16.pwmb/211308228/882/3189/197/90.0/5.0,19/ResinDrainer1000ml_Cat2-on-side.pwmb/248.pwmb/147633505/502/2184/203/90.0/5.6,20/Sim Card Holder.pwmb/254.pwmb/9502956/28/48/13/60.0/8.0,21/Sim Card Holder.pwmb/17.pwmb/9502956/28/48/13/60.0/8.0,22/Sim Card Holder.pwmb/35.pwmb/9502956/33/48/13/95.0/6.0,23/Sim Card Holder.pwmb/53.pwmb/9502956/32/48/13/80.0/6.0,24/Sim Card Holder.pwmb/71.pwmb/9502956/27/48/13/60.0/8.0,25/3x Sim Holder 2022-05-22.pwmb/55.pwmb/15022287/21/48/13/90.0/4.2,26/3x Sim Holder 2022-05-22.pwmb/74.pwmb/15022287/29/48/13/90.0/4.2,27/3x Sim Holder 2022-05-22.pwmb/93.pwmb/15022287/17/48/13/60.0/3.0,28/Sim Card Holder.pwmb/99.pwmb/9502956/25/48/13/80.0/4.0,29/Sim Card Holder.pwmb/118.pwmb/9502956/21/48/13/40.0/3.0,30/Sim Card Holder.pwmb/137.pwmb/9502956/20/48/13/60.0/4.0,31/2-phone stands better supports.pwmb/98.pwmb/257584892/1021/1726/390/90.0/4.0,32/SLA print puller supported.pwmb/100.pwmb/6416188/215/482/2/40.0/4.0,33/Random Stuff.pwmb/143.pwmb/36502627/376/1683/53/60.0/4.0,34/Validation_Matrix.pwmb/173.pwmb/4563150/15/38/5/60.0/4.0,35/Various Hooks.pwmb/45.pwmb/43905000/223/951/63/70.0/4.0,36/SLA print puller supported.pwmb/71.pwmb/6416188/1466/482/2/40.0/4.0,37/2-phone stands better supports.pwmb/138.pwmb/257584892/1304/1726/390/60.0/4.0,38/MonoX Bottle Drainer.pwmb/55.pwmb/3890366/30/100/8/70.0/4.0,39/MonoX Bottle Drainer mk2.pwmb/144.pwmb/3839217/30/100/8/70.0/4.0,40/MonoX Bottle Drainer mk3.pwmb/221.pwmb/3937731/30/100/9/70.0/4.0,41/MonoX Bottle Drainer mk3.pwmb/246.pwmb/3937731/30/100/9/70.0/4.0,42/MonoX Bottle Drainer mk3.pwmb/66.pwmb/3937731/30/100/9/70.0/4.0,43/MonoX Bottle Drainer mk4.pwmb/93.pwmb/3683630/27/100/9/60.0/3.0,44/shower shelf with suction cup.pwmb/52.pwmb/31173277/192/966/42/60.0/3.0,45/Phone Holder with Storage.pwmb/223.pwmb/61552748/1293/2153/199/60.0/3.0,46/Phone Holder with Storage mk2.pwmb/253.pwmb/136400508/518/2618/356/60.0/3.0,47/Phone Holder with Storage mk3.pwmb/129.pwmb/62595092/764/2344/155/90.0/2.5,48/Phone Holder with Storage mk3 2.pwmb/226.pwmb/133278095/477/2560/337/90.0/3.0,49/Phone Holder with Storage mk3.pwmb/65.pwmb/62595092/434/2344/155/90.0/3.0,50/Phone Holder with Storage mk3.pwmb/96.pwmb/62595092/607/2344/155/90.0/3.0,51/Phone Holder with Storage mk4.pwmb/224.pwmb/58908880/392/2206/175/70.0/3.0,52/Phone Holder mk5 Charger mk1.pwmb/184.pwmb/60881827/436/2396/151/90.0/3.0,53/Phone Holder Storage mk7 Charger.pwmb/5.pwmb/59128134/401/2396/151/70.0/3.0,54/Phone Holder mk5 Charger mk1 s.pwmb/138.pwmb/74621447/383/2396/155/90.0/3.0,55/Phone Holder with Storage mk7 2s.pwmb/178.pwmb/106446938/408/2206/352/90.0/3.0,56/Phone Holder with Storage mk7 2s.pwmb/56.pwmb/114692244/373/2206/358/70.0/3.0,57/AA_rpi_Touch_7inch car MK13.pwmb/119.pwmb/283054227/778/4191/220/90.0/3.0,58/Android Auto v13 Supported.pwmb/238.pwmb/523576464/1032/4768/345/70.0/4.0,59/Android Auto v13 Supported 2s.pwmb/96.pwmb/22318669/319/1040/18/40.0/3.0,60/SLA print puller supported.pwmb/107.pwmb/6416188/389/482/2/40.0/4.0,61/Android Auto v13 Supported 2s.pwmb/128.pwmb/539951910/1764/4830/352/70.0/4.0,62/Validation_Matrix.pwmb/166.pwmb/4563150/14/38/5/60.0/4.0,63/3DBenchy-heavy-antialias.pwmb/82.pwmb/22318669/1466/1040/18/40.0/4.0,64/Validation_Matrix.pwmb/139.pwmb/4563150/15/38/5/60.0/4.0,65/Validation_Matrix.pwmb/11.pwmb/4563150/15/38/5/60.0/4.0,66/Squiggley Clip.pwmb/0.pwmb/124079253/108/478/129/90.0/3.0,67/Phone Holder with Storage mk7 2s.pwmb/3.pwmb/114692244/399/2206/358/70.0/3.0,68/watering can supported.pwmb/219.pwmb/695352272/1557/3662/298/90.0/3.0,69/watering can supported.pwmb/240.pwmb/695352272/2868/3662/298/90.0/3.5,70/Squiggley Clip.pwmb/249.pwmb/124079253/108/478/129/90.0/3.0,71/Squiggley Clip.pwmb/0.pwmb/124079253/108/478/129/90.0/3.0,72/Phone Stand with HX200Q5 adapter.pwmb/14.pwmb/70066762/484/2333/156/90.0/4.0,73/Phone Holder with Storage mk7 2s.pwmb/3.pwmb/114692244/373/2206/358/70.0/3.0,74/Anycubic Mono X vat foot.pwmb/15.pwmb/1835986/38/132/0/90.0/4.0,75/Anycubic Mono X vat foot.pwmb/122.pwmb/2929221/58/236/0/90.0/4.0,76/Anycubic Mono X vat foot.pwmb/146.pwmb/2929221/58/236/0/90.0/4.0,77/Sim Switcher Parts.pwmb/154.pwmb/237051712/854/4100/362/90.0/4.0,78/Phone Holder with Storage mk7 2s.pwmb/3.pwmb/114692244/400/2206/358/70.0/3.0,79/Phone Holder with Storage mk7 2s.pwmb/28.pwmb/114692244/373/2206/358/70.0/3.0,80/Phone Holder with Storage mk7 2s.pwmb/53.pwmb/114692244/7389/2206/358/70.0/3.0,81/vase and dog stuff.pwmb/209.pwmb/221363022/432/1888/174/90.0/4.0,82/AA_Rpi_Touch_7inc.pwmb/4.pwmb/166554892/1016/4719/0/90.0/4.0,83/3DBenchy-heavy-antialias.pwmb/6.pwmb/22318669/220/1040/18/40.0/4.0,84/American_bd_Wine_Stopper.pwmb/120.pwmb/41215156/289/1269/60/90.0/5.02,85/American_bd_Wine_Stopper.pwmb/144.pwmb/41215156/289/1269/60/90.0/5.02,86/American_bd_Wine_Stopper2.pwmb/21.pwmb/37763775/212/1094/64/80.0/3.5,87/American_bd_Wine_Stopper2.pwmb/123.pwmb/37763775/226/1094/64/80.0/4.0,88/American_bd";
$newarray = explode(",", $data);

$output = (object)NULL;
$output->type = "monox";
$output->history = [];
foreach ($newarray as $historicalItem) {
    if (startsWith($historicalItem, "gethistory")) {
        continue;
    }
    if (startsWith($historicalItem, "end")) {
        break;
    }
    $historicalItems = explode("/", $historicalItem);
    $item = (object)NULL;
    switch (sizeof($historicalItems)) {
        case 9:
            $item->HistoricalID = $historicalItems[0];
            $item->FileName = $historicalItems[1];
            $item->FileInternalName = $historicalItems[2];
            $item->FileSizeOnDiskBytes = $historicalItems[3];
            $item->MinutesSpentPrinting = $historicalItems[4];
            $item->TotalLayers = $historicalItems[5];
            $item->ResinMLConsumed = $historicalItems[6];
            $item->BottomLayerTime = $historicalItems[7];
            $item->NormalLayerTime = $historicalItems[8];
    }
    array_push($output->history, $item);
}

echo json_encode($output);
