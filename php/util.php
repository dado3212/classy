<?php
  $departments = [
    "AAAS" => "Afr & AfrAmerican Studies",
    "AMEL" => "Asian/Mideast Lang/Lit",
    "AMES" => "Asian/Mideast Studies",
    "ANTH" => "Anthropology",
    "ARAB" => "Arabic",
    "ARTH" => "Art History",
    "ASTR" => "Astronomy",
    "BIOL" => "Biology",
    "CHEM" => "Chemistry",
    "CHIN" => "Chinese",
    "CLST" => "Classical Studies",
    "COCO" => "College Course",
    "COGS" => "Cognitive Science",
    "COLT" => "Comparative Literature",
    "COSC" => "Computer Science",
    "EARS" => "Earth Sciences",
    "ECON" => "Economics",
    "EDUC" => "Education",
    "ENGL" => "English",
    "ENGS" => "Engineering Sciences",
    "ENVS" => "Environmental Studies",
    "FILM" => "Film Studies",
    "FREN" => "French",
    "FRIT" => "French/Italian in Translation",
    "FYS" => "First-Year Seminar",
    "GEOG" => "Geography",
    "GERM" => "German",
    "GOVT" => "Government",
    "GRK" => "Greek",
    "HEBR" => "Hebrew",
    "HIST" => "History",
    "HUM" => "Humanities",
    "INTS" => "International Studies",
    "ITAL" => "Italian",
    "JAPN" => "Japanese",
    "JWST" => "Jewish Studies",
    "LACS" => "Latin Am/Caribbean Studies",
    "LAT" => "Latin",
    "LATS" => "Latino Studies",
    "LING" => "Linguistics",
    "MATH" => "Mathematics",
    "MUS" => "Music",
    "NAS" => "Native American Studies",
    "PBPL" => "Public Policy",
    "PHIL" => "Philosophy",
    "PHYS" => "Physics",
    "PORT" => "Portuguese",
    "PSYC" => "Psychological & Brain Sciences",
    "QSS" => "Quantitative Social Science",
    "REL" => "Religion",
    "RUSS" => "Russian",
    "SART" => "Studio Art",
    "SOCY" => "Sociology",
    "SPAN" => "Spanish",
    "SPEE" => "Speech",
    "THEA" => "Theater",
    "TUCK" => "Tuck",
    "WGSS" => "Women's, Gender, and Sexuality",
    "WRIT" => "Writing Program",
  ];

  $distribs = [
    "W" => "W",
    "NW" => "NW",
    "CI" => "CI",
    "ART" => "ART",
    "LIT" => "LIT",
    "TMV" => "TMV",
    "INT" => "INT",
    "SOC" => "SOC",
    "QDS" => "QDS",
    "SCI" => "SCI",
    "SLA" => "SLA",
    "TAS" => "TAS",
    "TLA" => "TLA",
  ];

  $periods = [
    "8" => "8",
    "9" => "9",
    "9L" => "9L",
    "9S" => "9S",
    "10" => "10",
    "2" => "2",
    "2A" => "2A",
    "10A" => "10A",
    "11" => "11",
    "12" => "12",
    "3A" => "3A",
    "3B" => "3B",
    "6A" => "6A",
    "6B" => "6B",
  ];

  $medians = [
    "A" =>      6,
    "A/A-" =>   5.5,
    "A-" =>     5,
    "A-/B+" =>  4.5,
    "B+" =>     4,
    "B+/B" =>   3.5,
    "B" =>      3,
    "B/B-" =>   2.5,
    "B-" =>     2,
    "B-/C+" =>  1.5,
    "C+" =>     1,
    "C+/C" =>   0.5,
    "C" =>      0,
  ];

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
      return null;
    } else {
      $classes = [];

      preg_match_all("/<th CLASS=\"ddheader\" scope=\"col\" >Subject<\/th>[\s\S]*?<tr>\n<th/", $output, $matches);

      foreach ($matches[0] as $group) {
        preg_match_all("/<tr>\n<td CLASS=\"dddefault\">(.*?)<\/td>\n<td.*?>(.*?)<\/td>\n(?:<td class=\"dddefault\">UG<\/td>\n)*<td.*?colspan.*?>(.*?)<\/td>/i", $group, $rawClasses);

        for ($i = 0; $i < count($rawClasses[0]); $i++) {
          $classes[] = [
            "department" => $rawClasses[1][$i],
            "number" => $rawClasses[2][$i],
            "title" => $rawClasses[3][$i],
          ];
        }
      }

      return $classes;
    }
  }

  function getKeycode($agent) {
    if (stripos($agent, "Mac OS X") !== false) { // it's a mac
        if (stripos($agent, "Chrome") !== false) {
            return "Cmd+Opt+J";
        } else if (stripos($agent, "Safari") !== false) {
            return "Cmd+Opt+C";
        }
    }
    if ( stripos($agent, 'Firefox') !== false ) {
        return 'firefox';
    } elseif ( stripos($agent, 'MSIE') !== false ) {
        return 'ie';
    } elseif ( stripos($agent, 'iPad') !== false ) {
        return 'ipad';
    } elseif ( stripos($agent, 'Android') !== false ) {
        return 'android';
    } elseif ( stripos($agent, 'Chrome') !== false ) {
        return 'chrome';
    } elseif ( stripos($agent, 'Safari') !== false ) {
        return 'safari';
    }
  }
?>