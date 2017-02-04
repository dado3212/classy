<?php
  function getClasses($sessid) {
    $ch = curl_init("https://banner.dartmouth.edu/banner/groucho/bwskotrn.P_ViewTran");
    curl_setopt($ch, CURLOPT_COOKIE, "SESSID=$sessid" );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_REFERER, "https://banner.dartmouth.edu/banner/groucho/bwskotrn.P_ViewTermTran");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "levl=&tprt=GRUN");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    if (strpos($output, "A break in attempt has been detected!  Please login again.") !== false) {
      return [];
    } else {
      $classes = [];

      preg_match_all("/<th CLASS=\"ddheader\" scope=\"col\" >Subject<\/th>[\s\S]*?<tr>\n<th/", $output, $matches);

      foreach ($matches[0] as $group) {
        preg_match_all("/<tr>\n<td CLASS=\"dddefault\">(.*?)<\/td>\n<td.*?>(.*?)<\/td>\n(?:<td class=\"dddefault\">UG<\/td>\n)*<td.*?colspan.*?>(.*?)<\/td>/i", $group, $rawClasses);

        for ($i = 0; $i < count($rawClasses[0]); $i++) {
          $classes[] = [
            "dept" => $rawClasses[1][$i],
            "course" => $rawClasses[2][$i],
            "name" => $rawClasses[3][$i],
          ];
        }
      }

      return $classes;
    }
  }
?>