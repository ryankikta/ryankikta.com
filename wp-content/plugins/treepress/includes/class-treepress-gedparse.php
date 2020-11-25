<?php
class GedParse {

  public function parse($file){
    $anfang   = 0;
    $anfangf  = 0;
    $persons   = array( );
    $families = array( );
    $chil     = array( );
    $birt     = 0;
    $deat     = 0;
    $chr      = 0;
    $buri     = 0;
    $occu     = 0;
    $conf     = 0;
    $indi     = null;
    $surn     = null;
    $givn     = null;
    $marn     = null;
    $sex      = null;
    $birtplac = null;
    $birtdate = null;
    $deatplac = null;
    $deatdate = null;
    $chrdate  = null;
    $chrplac  = null;
    $buridate = null;
    $buriplac = null;
    $reli     = null;
    $occu2    = null;
    $occudate = null;
    $occuplac = null;
    $confdate = null;
    $confplac = null;
    $note     = null;
    $famlist  = null;
    $marr     = 0;
    $marrdate = null;
    $marrplac = null;
    $famindi  = null;
    $husb     = null;
    $wife     = null;
    $lines    = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    for ( $i = 0; $i < count( $lines ); $i++ ) {
        if ( preg_match( "/0\x20\x40(I.*)\x40/", $lines[ $i ], $id ) ) {
            if ( $anfang == 1 ) {
                array_push( $persons, array(
                    'id' => $indi,
                    'surn' => $surn,
                    'givn' => $givn,
                    'marn' => $marn,
                    'sex' => $sex,
                    'birtdate' => $birtdate,
                    'birtplac' => $birtplac,
                    'chrdate' => $chrdate,
                    'chrplac' => $chrplac,
                    'deatdate' => $deatdate,
                    'deatplac' => $deatplac,
                    'buridate' => $buridate,
                    'buriplac' => $buriplac,
                    'occu2' => $occu2,
                    'occudate' => $occudate,
                    'occuplac' => $occuplac,
                    'reli' => $reli,
                    'confdate' => $confdate,
                    'confplac' => $confplac,
                    'note' => $note 
                ) );
                $deat     = 0;
                $chr      = 0;
                $buri     = 0;
                $occu     = 0;
                $conf     = 0;
                $birt     = 0;
                $marr     = 0;
                $indi     = null;
                $surn     = null;
                $givn     = null;
                $marn     = null;
                $sex      = null;
                $birtplac = null;
                $birtdate = null;
                $deatplac = null;
                $deatdate = null;
                $chrdate  = null;
                $chrplac  = null;
                $buridate = null;
                $buriplac = null;
                $reli     = null;
                $occu2    = null;
                $occudate = null;
                $occuplac = null;
                $confdate = null;
                $confplac = null;
                $note     = null;
            }
            $indi   = $id[ 1 ];
            $indi   = str_replace( "\x27", "\xB4", $indi );
            $anfang = 1;
        } else if ( preg_match( "/2\x20SURN\x20(.*)/", $lines[ $i ], $surnA ) ) {
            $surn = str_replace( "\x27", "\xB4", $surnA[ 1 ] );
        } else if ( preg_match( "/2\x20GIVN\x20(.*)/", $lines[ $i ], $givnA ) ) {
            $givn = str_replace( "\x27", "\xB4", $givnA[ 1 ] );
        } else if ( preg_match( "/1\x20NAME\x20(.*)/", $lines[ $i ], $nameA ) ) {
            $full = str_replace( "\x27", "\xB4", $nameA[ 1 ] );
            $tmp  = preg_split( "/\//", $full );
            $givn = $tmp[ 0 ];
            $surn = $tmp[ 1 ];
        } else if ( preg_match( "/2\x20_MARNM\x20(.*)/", $lines[ $i ], $marnA ) ) {
            $marn = str_replace( "\x27", "\xB4", $marnA[ 1 ] );
        } else if ( preg_match( "/1\x20SEX\x20(.*)/", $lines[ $i ], $sexA ) ) {
            $sex = $sexA[ 1 ];
        } else if ( preg_match( "/1\x20BIRT/", $lines[ $i ] ) ) {
            $deat = 0;
            $chr  = 0;
            $buri = 0;
            $occu = 0;
            $conf = 0;
            $birt = 1;
            $marr = 0;
        } else if ( preg_match( "/1\x20RESI/", $lines[ $i ] ) ) {
            $deat = 0;
            $chr  = 0;
            $buri = 0;
            $occu = 0;
            $conf = 0;
            $birt = 0;
            $marr = 0;
        } else if ( preg_match( "/1\x20DEAT/", $lines[ $i ] ) ) {
            $birt = 0;
            $chr  = 0;
            $buri = 0;
            $occu = 0;
            $conf = 0;
            $deat = 1;
            $marr = 0;
        } else if ( preg_match( "/1\x20CHR/", $lines[ $i ] ) ) {
            $birt = 0;
            $deat = 0;
            $chr  = 1;
            $buri = 0;
            $occu = 0;
            $conf = 0;
            $marr = 0;
        } else if ( preg_match( "/1\x20BURI/", $lines[ $i ] ) ) {
            $birt = 0;
            $deat = 0;
            $chr  = 0;
            $buri = 1;
            $occu = 0;
            $conf = 0;
            $marr = 0;
        } else if ( preg_match( "/1\x20OCCU\x20(.*)/", $lines[ $i ], $occu2A ) ) {
            $birt  = 0;
            $deat  = 0;
            $chr   = 0;
            $buri  = 0;
            $occu  = 1;
            $conf  = 0;
            $marr  = 0;
            $occu2 = str_replace( "\x27", "\xB4", $occu2A[ 1 ] );
        } else if ( preg_match( "/1\x20CONF/", $lines[ $i ] ) ) {
            $birt = 0;
            $deat = 0;
            $chr  = 0;
            $buri = 0;
            $occu = 0;
            $conf = 1;
            $marr = 0;
        } else if ( preg_match( "/1\x20MARR/", $lines[ $i ] ) ) {
            $marr = 1;
        } else if ( preg_match( "/2\x20DATE\x20(.*)/", $lines[ $i ], $givenDate ) ) {
            if ( $birt == 1 ) {
                $birtdate = $givenDate[ 1 ];
                $birtdate = str_replace( "\x27", "\xB4", $birtdate );
            }
            if ( $deat == 1 ) {
                $deatdate = $givenDate[ 1 ];
                $deatdate = str_replace( "\x27", "\xB4", $deatdate );
            }
            if ( $chr == 1 ) {
                $chrdate = $givenDate[ 1 ];
                $chrdate = str_replace( "\x27", "\xB4", $chrdate );
            }
            if ( $buri == 1 ) {
                $buridate = $givenDate[ 1 ];
                $buridate = str_replace( "\x27", "\xB4", $buridate );
            }
            if ( $occu == 1 ) {
                $occudate = $givenDate[ 1 ];
                $occudate = str_replace( "\x27", "\xB4", $occudate );
            }
            if ( $conf == 1 ) {
                $confdate = $givenDate[ 1 ];
                $confdate = str_replace( "\x27", "\xB4", $confdate );
            }
            if ( $marr == 1 ) {
                $marrdate = $givenDate[ 1 ];
                $marrdate = str_replace( "\x27", "\xB4", $marrdate );
            }
        } else if ( preg_match( "/2\x20PLAC\x20(.*)/", $lines[ $i ], $givenPlac ) ) {
            if ( $birt == 1 ) {
                $birtplac = $givenPlac[ 1 ];
                $birtplac = str_replace( "\x27", "\xB4", $birtplac );
            }
            if ( $deat == 1 ) {
                $deatplac = $givenPlac[ 1 ];
                $deatplac = str_replace( "\x27", "\xB4", $deatplac );
            }
            if ( $chr == 1 ) {
                $chrplac = $givenPlac[ 1 ];
                $chrplac = str_replace( "\x27", "\xB4", $chrplac );
            }
            if ( $buri == 1 ) {
                $buriplac = $givenPlac[ 1 ];
                $buriplac = str_replace( "\x27", "\xB4", $buriplac );
            }
            if ( $occu == 1 ) {
                $occuplac = $givenPlac[ 1 ];
                $occuplac = str_replace( "\x27", "\xB4", $occuplac );
            }
            if ( $conf == 1 ) {
                $confplac = $givenPlac[ 1 ];
                $confplac = str_replace( "\x27", "\xB4", $confplac );
            }
            if ( $marr == 1 ) {
                $marrplac = $givenPlac[ 1 ];
                $marrplac = str_replace( "\x27", "\xB4", $marrplac );
            }
        } else if ( preg_match( "/1\x20RELI\x20(.*)/", $lines[ $i ], $reliA ) ) {
            $reli = str_replace( "\x27", "\xB4", $reliA[ 1 ] );
        } else if ( preg_match( "/1\x20NOTE\x20(.*)/", $lines[ $i ], $noteA ) ) {
            $note = str_replace( "\x27", "\xB4", $noteA[ 1 ] );
        } else if ( preg_match( "/2\x20CONC\x20(.*)/", $lines[ $i ], $concA ) ) {
            $note .= $concA[ 1 ];
            $note = str_replace( "\x27", "\xB4", $note );
        } else if ( preg_match( "/0\x20\x40(F.*)\x40/", $lines[ $i ], $famindiA ) ) {
            if ( $anfangf == 1 ) {
                $famlist = array(
                     'famindi' => $famindi,
                    'husb' => $husb,
                    'wife' => $wife,
                    'marrdate' => $marrdate,
                    'marrplac' => $marrplac,
                    'chil' => array( ) 
                );
                foreach ( $chil as $entry ) {
                    $famlist[ 'chil' ][ ] = $entry;
                }
                array_push( $families, $famlist );
                $famlist  = null;
                $marr     = 0;
                $marrdate = null;
                $marrplac = null;
                $famindi  = null;
                $husb     = null;
                $wife     = null;
                $chil     = array( );
            }
            if ( $anfangf == 0 ) {
                array_push( $persons, array(
                     'id' => $indi,
                    'surn' => $surn,
                    'givn' => $givn,
                    'marn' => $marn,
                    'sex' => $sex,
                    'birtdate' => $birtdate,
                    'birtplac' => $birtplac,
                    'chrdate' => $chrdate,
                    'chrplac' => $chrplac,
                    'deatdate' => $deatdate,
                    'deatplac' => $deatplac,
                    'buridate' => $buridate,
                    'buriplac' => $buriplac,
                    'occu2' => $occu2,
                    'occudate' => $occudate,
                    'occuplac' => $occuplac,
                    'reli' => $reli,
                    'confdate' => $confdate,
                    'confplac' => $confplac,
                    'note' => $note 
                ) );
                $birt     = 0;
                $deat     = 0;
                $chr      = 0;
                $buri     = 0;
                $occu     = 0;
                $conf     = 0;
                $indi     = null;
                $surn     = null;
                $givn     = null;
                $sex      = null;
                $birtplac = null;
                $birtdate = null;
                $deatplac = null;
                $deatdate = null;
                $chrdate  = null;
                $chrplac  = null;
                $buridate = null;
                $buriplac = null;
                $reli     = null;
                $occu2    = null;
                $occudate = null;
                $occuplac = null;
                $confdate = null;
                $confplac = null;
                $note     = null;
                $anfangf  = 1;
            }
            $famindi = $famindiA[ 1 ];
        } else if ( preg_match( "/1\x20HUSB\x20\x40(.*)\x40/", $lines[ $i ], $husbA ) ) {
            $husb = str_replace( "\x27", "\xB4", $husbA[ 1 ] );
        } else if ( preg_match( "/1\x20WIFE\x20\x40(.*)\x40/", $lines[ $i ], $wifeA ) ) {
            $wife = str_replace( "\x27", "\xB4", $wifeA[ 1 ] );
        } else if ( preg_match( "/1\x20CHIL\x20\x40(.*)\x40/", $lines[ $i ], $cA ) ) {
            $c = str_replace( "\x27", "\xB4", $cA[ 1 ] );
            array_push( $chil, $c );
        } else if ( preg_match( "/1\x20CHAN/", $lines[ $i ] ) ) {
            $deat = 0;
            $chr  = 0;
            $buri = 0;
            $occu = 0;
            $conf = 0;
            $birt = 0;
            $marr = 0;
        } else if ( preg_match( "/0\x20TRLR/", $lines[ $i ] ) ) {
            $famlist = array(
                 'famindi' => $famindi,
                'husb' => $husb,
                'wife' => $wife,
                'marrdate' => $marrdate,
                'marrplac' => $marrplac,
                'chil' => array( ) 
            );
            foreach ( $chil as $entry ) {
                $famlist[ 'chil' ][ ] = $entry;
            }
            array_push( $families, $famlist );
        }
    }

    return array(
      'persons' => $persons,
      'families' => $families,
    );
    
  }
}