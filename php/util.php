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

  $terms = [
    "18F",
    "18X",
    "18S",
    "18W",
    "17F",
    "17S",
    "16W",
    "16S",
  ];

  function getClassesFromString($string) {
    preg_match_all("/Subject.*?Grade[\s]*R\n((.+?)[\s]*(\d+).*\n)*/", $string, $sections);

    $classes = [];
    foreach ($sections[0] as $section) {
      $rawClasses = explode("\n", $section);
      if (count($rawClasses) > 1) {
        foreach ($rawClasses as $rawClass) {
          preg_match("/(.*?)[\s]*(\d+).*/", $rawClass, $classMatch);
          if (count($classMatch) == 3) {
            $classes[] = [
              "department" => $classMatch[1],
              "number" => $classMatch[2],
            ];
          }
        }
      }
    }
    return $classes;
  }
?>