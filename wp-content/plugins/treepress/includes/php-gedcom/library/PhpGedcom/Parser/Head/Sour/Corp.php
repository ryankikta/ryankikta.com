<?php
/**
 * php-gedcom
 *
 * php-gedcom is a library for parsing, manipulating, importing and exporting
 * GEDCOM 5.5 files in PHP 5.3+.
 *
 * @author          Kristopher Wilson <kristopherwilson@gmail.com>
 * @copyright       Copyright (c) 2010-2013, Kristopher Wilson
 * @package         php-gedcom
 * @license         GPL-3.0
 * @link            http://github.com/mrkrstphr/php-gedcom
 */

namespace PhpGedcom\Parser\Head\Sour;

/**
 *
 *
 */
class Corp extends \PhpGedcom\Parser\Component
{

    /**
     *
     *
     */
    public static function parse(\PhpGedcom\Parser $parser)
    {
        $record = $parser->getCurrentLineRecord();
        $depth = (int)$record[0];

        $corp = new \PhpGedcom\Record\Head\Sour\Corp();
        $corp->setCorp(trim($record[2]));

        $parser->forward();

        while (!$parser->eof()) {
            $record = $parser->getCurrentLineRecord();
            $recordType = strtoupper(trim($record[1]));
            $currentDepth = (int)$record[0];

            if ($currentDepth <= $depth) {
                $parser->back();
                break;
            }

            switch ($recordType) {
                case 'ADDR':
                    $corp->setAddr(\PhpGedcom\Parser\Addr::parse($parser));
                    break;
                case 'PHON':
                    $corp->addPhon(trim($record[2]));
                    break;
                default:
                    $parser->logUnhandledRecord(get_class() . ' @ ' . __LINE__);
            }

            $parser->forward();
        }

        return $corp;
    }
}
