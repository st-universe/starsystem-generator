<?php

namespace Stu\StarsystemGenerator;

use DirectoryIterator;
use Generator;
use Stu\StarsystemGenerator\Component\SizeGeneratorInterface;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Exception\StarsystemGeneratorFileMissingException;

final class StarsystemGenerator implements StarsystemGeneratorInterface
{

    public const BASEWEIGHT = 'baseweight';
    public const WEIGHT = 'weight';
    public const AFFX = 'affx';
    public const AFFY = 'affy';
    public const FROM = 'from';
    public const TO = 'to';
    public const ADJACENT = 'adjacent';
    public const NOADJACENT = 'noadjacent';
    public const NOADJACENTLIMIT = 'noadjacentlimit';
    public const NUM = 'num';
    public const MODE = 'more';
    public const FRAGMENTATION = 'fragmentation';
    public const X = 'x';
    public const Y = 'y';
    public const WIDTH = 'width';
    public const IBORDER = 'iborder';
    public const OBORDER = 'oborder';
    public const STAR = 'star';
    public const EVEN = 'even';
    public const ODD = 'odd';
    public const RADIUS = 'radius';
    public const TYPE = 'type';
    public const DESCRIPTION = 'description';
    public const MOONS = 'moons';
    public const NAME = 'name';
    public const PLANETS = 'planets';

    private SizeGeneratorInterface $sizeGenerator;

    public function __construct(SizeGeneratorInterface $sizeGenerator)
    {
        $this->sizeGenerator = $sizeGenerator;
    }

    /**
     * - array aufbauen, size random
     *      - array in Object, als Param in die einzelnen Generatoren rein
     *      - 7 bis 27
     * - anhand size -> stern / binär etc.
     *      - von field_id in stu_map die beiden rechten Stellen
     *          abschneiden um zur ID von stu_system_types zu kommen
     *      - typen in stu_system_types
     *      - gerade anzahl: 2x2, binäre
     *      - ungerade: 1x1, 3x3, binäre
     *      - bonusFelder random
     *      - Name raussuchen stu_system_name
     * - 0,1,2 ringe (abhängig von size)
     *      - dichte, abhängig vom letzten Feld davor
     *      - position
     * - planeten verteilen
     * - monde verteilen
     * - Vorschau rendern
     */

    public function generate(int $systemType): SystemMapDataInterface
    {
        $config = $this->loadSystemTypeConfiguration($systemType);

        $mapData = $this->sizeGenerator->generate($config);

        return $mapData;
    }

    /**
     * @throws StarsystemGeneratorFileMissingException
     */
    private function loadSystemTypeConfiguration(int $systemType): SystemConfigurationInterface
    {
        $fileName = sprintf(
            '%s/Config/Data/%d.php',
            __DIR__,
            $systemType
        );
        if (!file_exists($fileName)) {
            throw new StarsystemGeneratorFileMissingException('Systemgenerator description file missing for systemType ' . $systemType);
        }
        $requireResult = require $fileName;

        if (!$requireResult instanceof SystemConfigurationInterface) {
            throw new StarsystemGeneratorFileMissingException('Error loading Systemgenerator description file for systemType ' . $systemType);
        }

        return $requireResult;
    }

    public function getSupportedSystemTypes(): Generator
    {
        $list = new DirectoryIterator(__DIR__ . '/Config/Data');

        foreach ($list as $file) {
            if (!$file->isDir()) {
                yield (int) str_replace('.php', '', $file->getFilename());
            }
        }
    }

    // NEUER CODE
    function drawCircle(array &$array, $centerX, $centerY, $radius)
    {
        $width = count($array[0]);
        $height = count($array);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $distance = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));
                if ($distance <= $radius) {
                    $array[$y][$x] = '*'; // Use '*' to mark the circle
                }
            }
        }
    }


    //ALTER CODE
    private function draw($arr): int
    {
        $p = rand(1, 100);
        $c = 0;
        for ($i = 0; $i < 20; $i++) {
            $c += $arr[$i];
            if ($p <= $c) {
                return $i;
            }
        }
        return 0;
    }

    private function pdraw($arr): int
    {
        $p = rand(1, 100);
        $c = 0;
        for ($i = 100; $i < 200; $i++) {
            $c += $arr[$i];
            if ($p <= $c) {
                return $i;
            }
        }
        return 0;
    }

    private function weightedDraw($a, $fragmentation = 0): array
    {
        for ($i = 0; $i < count($a); $i++) {
            $a[$i][self::WEIGHT] = rand(1, ceil($a[$i][self::BASEWEIGHT] + $fragmentation));
        }
        usort(
            $a,
            function ($a, $b) {
                if ($a[self::WEIGHT] < $b[self::WEIGHT]) {
                    return +1;
                }
                if ($a[self::WEIGHT] > $b[self::WEIGHT]) {
                    return -1;
                }
                return (rand(1, 3) - 2);
            }
        );
        return $a[0];
    }

    private function getWeightingList($colfields, $mode, $from, $to, $adjacent, $noadjacent, $noadjacentlimit = 0)
    {
        $w = count($colfields);
        $h = count($colfields[1]);
        $c = 0;
        for ($i = 1; $i <= $h; $i++) {
            for ($j = 1; $j <= $w; $j++) {
                $skip = 1;
                for ($k = 0; $k < count($from); $k++) {
                    if ($colfields[$j][$i] == $from[$k]) {
                        $skip = 0;
                    }
                }
                if ($skip == 1) {
                    continue;
                }

                $bw = 1;

                if ($mode != "nocluster" && $mode != "forced adjacency") {
                    for ($k = 0; $k < count($to); $k++) {
                        if ($colfields[$j - 1][$i] == $to[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j + 1][$i] == $to[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j][$i - 1] == $to[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j][$i + 1] == $to[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j - 1][$i - 1] == $to[$k]) {
                            $bw += 0.5;
                        }
                        if ($colfields[$j + 1][$i + 1] == $to[$k]) {
                            $bw += 0.5;
                        }
                        if ($colfields[$j + 1][$i - 1] == $to[$k]) {
                            $bw += 0.5;
                        }
                        if ($colfields[$j - 1][$i + 1] == $to[$k]) {
                            $bw += 0.5;
                        }
                    }
                }

                if ($adjacent[0]) {
                    for ($k = 0; $k < count($adjacent); $k++) {
                        if ($colfields[$j - 1][$i] == $adjacent[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j + 1][$i] == $adjacent[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j][$i - 1] == $adjacent[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j][$i + 1] == $adjacent[$k]) {
                            $bw += 1;
                        }
                        if ($colfields[$j - 1][$i - 1] == $adjacent[$k]) {
                            $bw += 0.5;
                        }
                        if ($colfields[$j + 1][$i + 1] == $adjacent[$k]) {
                            $bw += 0.5;
                        }
                        if ($colfields[$j + 1][$i - 1] == $adjacent[$k]) {
                            $bw += 0.5;
                        }
                        if ($colfields[$j - 1][$i + 1] == $adjacent[$k]) {
                            $bw += 0.5;
                        }
                    }
                }

                if ($noadjacent[0]) {
                    for ($k = 0; $k < count($noadjacent); $k++) {
                        $ad = 0;
                        if ($colfields[$j - 1][$i] == $noadjacent[$k]) {
                            $ad += 1;
                        }
                        if ($colfields[$j + 1][$i] == $noadjacent[$k]) {
                            $ad += 1;
                        }
                        if ($colfields[$j][$i - 1] == $noadjacent[$k]) {
                            $ad += 1;
                        }
                        if ($colfields[$j][$i + 1] == $noadjacent[$k]) {
                            $ad += 1;
                        }
                        if ($colfields[$j - 1][$i - 1] == $noadjacent[$k]) {
                            $ad += 0.5;
                        }
                        if ($colfields[$j + 1][$i + 1] == $noadjacent[$k]) {
                            $ad += 0.5;
                        }
                        if ($colfields[$j + 1][$i - 1] == $noadjacent[$k]) {
                            $ad += 0.5;
                        }
                        if ($colfields[$j - 1][$i + 1] == $noadjacent[$k]) {
                            $ad += 0.5;
                        }

                        if ($ad > $noadjacentlimit) {
                            $bw = 0;
                        }
                    }
                }

                if ($bw > 0) {
                    $res[$c][self::X] = $j;
                    $res[$c][self::Y] = $i;
                    $res[$c][self::BASEWEIGHT] = $bw;
                    $c++;
                }
            }
        }
        return $res;
    }

    private function doPhase($p, $phase, $colfields)
    {
        $colfields[self::AFFX] = 0;
        $colfields[self::AFFY] = 0;
        for ($i = 0; $i < $phase[$p][self::NUM]; $i++) {
            $arr = $this->getWeightingList(
                $colfields,
                $phase[$p][self::MODE],
                $phase[$p][self::FROM],
                $phase[$p][self::TO],
                $phase[$p][self::ADJACENT],
                $phase[$p][self::NOADJACENT],
                $phase[$p][self::NOADJACENTLIMIT]
            );
            if (count($arr) == 0) {
                break;
            }

            $field = $this->weightedDraw($arr, $phase[$p][self::FRAGMENTATION]);
            $ftype = $colfields[$field[self::X]][$field[self::Y]];

            $t = 0;
            unset($ta);
            for ($c = 0; $c < count($phase[$p][self::FROM]); $c++) {
                if ($ftype == $phase[$p][self::FROM][$c]) {
                    $ta[$t] = $phase[$p][self::TO][$c];
                    $t++;
                }
            }
            if ($t > 0) {
                $colfields[$field[self::X]][$field[self::Y]] = $ta[rand(0, $t - 1)];
            }
            $colfields[self::AFFX] = $field[self::X];
            $colfields[self::AFFY] = $field[self::Y];
        }
        return $colfields;
    }

    private function polarToXY($angle, $distance): array
    {
        $res = [];
        $angle = $angle * pi() / 180;
        $res[self::X] = floor(cos($angle) * $distance);
        $res[self::Y] = floor(sin($angle) * $distance);
        return $res;
    }

    private function collides($star1, $star2): bool
    {
        $ul1x = $star1[self::X] - floor($star1[self::WIDTH] / 2);
        $ul1y = $star1[self::Y] - floor($star1[self::WIDTH] / 2);

        $ul2x = $star2[self::X] - floor($star2[self::WIDTH] / 2);
        $ul2y = $star2[self::Y] - floor($star2[self::WIDTH] / 2);

        for ($i = ($ul1x - $star1[self::IBORDER]); $i <= ($ul1x + $star1[self::WIDTH] + $star1[self::IBORDER] - 1); $i++) {
            for ($j = ($ul1y - $star1[self::IBORDER]); $j <= ($ul1y + $star1[self::WIDTH] + $star1[self::IBORDER] - 1); $j++) {
                $fields[$i][$j] = 1;
            }
        }

        for ($i = $ul1x; $i <= ($ul1x + $star1[self::WIDTH] - 1); $i++) {
            for ($j = $ul1y; $j <= ($ul1y + $star1[self::WIDTH] - 1); $j++) {
                $fields[$i][$j] = 2;
            }
        }

        for ($i = ($ul2x - $star2[self::IBORDER]); $i <= ($ul2x + $star2[self::WIDTH] + $star2[self::IBORDER] - 1); $i++) {
            for ($j = ($ul2y - $star2[self::IBORDER]); $j <= ($ul2y + $star2[self::WIDTH] + $star2[self::IBORDER] - 1); $j++) {
                if ($fields[$i][$j] == 2) {
                    return true;
                }
            }
        }

        for ($i = $ul2x; $i <= ($ul2x + $star2[self::WIDTH] - 1); $i++) {
            for ($j = $ul2y; $j <= ($ul2y + $star2[self::WIDTH] - 1); $j++) {
                if ($fields[$i][$j] == 2 || $fields[$i][$j] == 1) {
                    return true;
                }
            }
        }


        return false;
    }

    private function makeCoreZone(array $star, int $stars)
    {
        $star[0][self::X] = 0;
        $star[0][self::Y] = 0;
        $a = rand(1, 360);

        if ($stars == 2) {
            $distance = 0;
            $c = $this->polarToXY($a, $distance);
            $star[1][self::X] = $c[self::X];
            $star[1][self::Y] = $c[self::Y];
            while ($this->collides($star[0], $star[1])) {
                $distance++;
                $c = $this->polarToXY($a, $distance);
                $star[1][self::X] = $c[self::X];
                $star[1][self::Y] = $c[self::Y];
            }
        } elseif ($stars == 3) {
            $distance = 0;
            $c = $this->polarToXY($a, $distance);
            $star[1][self::X] = $c[self::X];
            $star[1][self::Y] = $c[self::Y];
            while ($this->collides($star[0], $star[1])) {
                $distance++;
                $c = $this->polarToXY($a, $distance);
                $star[1][self::X] = $c[self::X];
                $star[1][self::Y] = $c[self::Y];
            }
            $a += 60;
            $distance = 0;
            $c = $this->polarToXY($a, $distance);
            $star[2][self::X] = $c[self::X];
            $star[2][self::Y] = $c[self::Y];
            while ($this->collides($star[1], $star[2]) || $this->collides($star[0], $star[2])) {
                $distance++;
                $c = $this->polarToXY($a, $distance);
                $star[2][self::X] = $c[self::X];
                $star[2][self::Y] = $c[self::Y];
            }
        } elseif ($stars == 4) {
            $distance = 0;
            $c = $this->polarToXY($a, $distance);
            $star[1][self::X] = $c[self::X];
            $star[1][self::Y] = $c[self::Y];
            while ($this->collides($star[0], $star[1])) {
                $distance++;
                $c = $this->polarToXY($a, $distance);
                $star[1][self::X] = $c[self::X];
                $star[1][self::Y] = $c[self::Y];
            }
            $a += 90;
            $distance = 0;
            $c = $this->polarToXY($a, $distance);
            $star[2][self::X] = $c[self::X];
            $star[2][self::Y] = $c[self::Y];
            while ($this->collides($star[1], $star[2]) || $this->collides($star[0], $star[2])) {
                $distance++;
                $c = $this->polarToXY($a, $distance);
                $star[2][self::X] = $c[self::X];
                $star[2][self::Y] = $c[self::Y];
            }
            $a -= 45;
            $distance = 0;
            $c = $this->polarToXY($a, $distance);
            $star[3][self::X] = $c[self::X];
            $star[3][self::Y] = $c[self::Y];
            while ($this->collides($star[0], $star[3]) || $this->collides(
                $star[1],
                $star[3]
            ) || $this->collides($star[2], $star[3])) {
                $distance++;
                $c = $this->polarToXY($a, $distance);
                $star[3][self::X] = $c[self::X];
                $star[3][self::Y] = $c[self::Y];
            }
        }


        return $this->centralize($star, $stars);
    }

    private function randround($x)
    {
        $r = rand(1, 2);
        if ($r == 1) {
            return ceil($x);
        } else {
            return floor($x);
        }
    }

    private function centralize($star, $stars)
    {
        $minx = 1000;
        $miny = 1000;
        $maxx = -1000;
        $maxy = -1000;

        for ($i = 0; $i < $stars; $i++) {
            $ulx = $star[$i][self::X] - floor($star[$i][self::WIDTH] / 2);
            $uly = $star[$i][self::Y] - floor($star[$i][self::WIDTH] / 2);

            $infx = $ulx - $star[$i][self::OBORDER];
            $infy = $uly - $star[$i][self::OBORDER];

            $supx = $ulx + $star[$i][self::WIDTH] + $star[$i][self::OBORDER] - 1;
            $supy = $uly + $star[$i][self::WIDTH] + $star[$i][self::OBORDER] - 1;

            if ($infx < $minx) {
                $borderxstar = $i;
                $borderx = $ulx - $star[$i][self::OBORDER];
            }
            if ($infy < $miny) {
                $borderystar = $i;
                $bordery = $uly - $star[$i][self::OBORDER];
            }

            $minx = min($minx, $infx);
            $miny = min($miny, $infy);

            $maxx = max($maxx, $supx);
            $maxy = max($maxy, $supy);
        }

        $width = $maxx - $minx + 1;
        $height = $maxy - $miny + 1;

        $heightadd = 0;
        if (($width % 2) == 0) {
            if (($height % 2) == 1) {
                $height++;
                $heightadd = rand(0, 1);
            }
            $even = 1;
            $odd = 0;
        } else {
            if (($height % 2) == 0) {
                $height++;
                $heightadd = rand(0, 1);
            }
            $even = 0;
            $odd = 1;
        }


        $total = max($width, $height);

        if ($total > $width) {
            $conx = ($total - $width) / 2;
        }
        if ($total > $height) {
            $cony = ($total - $height) / 2;
        }

        $newx = 1;
        $newy = 1;


        $addx = $newx + $conx - $borderx;
        $addy = $newy + $cony - $bordery + $heightadd;


        for ($i = 0; $i < $stars; $i++) {
            $ulx = $star[$i][self::X] - floor($star[$i][self::WIDTH] / 2);
            $uly = $star[$i][self::Y] - floor($star[$i][self::WIDTH] / 2);

            $star[$i][self::X] = $ulx + $addx;
            $star[$i][self::Y] = $uly + $addy;
        }

        $res[self::STAR] = $star;
        $res[self::EVEN] = $even;
        $res[self::ODD] = $odd;
        $res[self::WIDTH] = $total;
        return $res;
    }

    private function distance($x, $y, $m, $n)
    {
        return sqrt(($x - $m) * ($x - $m) + ($y - $n) * ($y - $n));
    }


    public function generateOld($id)
    {
        list($star, $stars, $data, $zone, $belt, $belts) = require_once 'incSystems/' . $id . '.php';

        $r = $this->makeCoreZone($star, $stars);


        $syswidth = $r[self::ODD] + 2 * $data[self::RADIUS];
        if ($syswidth < $r[self::WIDTH]) {
            $syswidth = $r[self::WIDTH];
        }

        $borderadd = ($syswidth - $r[self::WIDTH]) / 2;

        for ($i = 1; $i <= $syswidth; $i++) {
            for ($j = 1; $j <= $syswidth; $j++) {
                $fields[$i][$j] = 1;
            }
        }

        for ($i = 1; $i <= $r[self::WIDTH]; $i++) {
            for ($j = 1; $j <= $r[self::WIDTH]; $j++) {
                $fields[$i + $borderadd][$j + $borderadd] = 0;
            }
        }

        $starrad = (1.4 * $r[self::WIDTH] / 2);
        $m = ($syswidth + 1) / 2;
        for ($i = 1; $i <= $syswidth; $i++) {
            for ($j = 1; $j <= $syswidth; $j++) {
                if (($fields[$i][$j] == 1) && ($this->distance($i, $j, $m, $m) < $starrad)) {
                    $fields[$i][$j] = 0;
                }
                if ($i <= 1 || $i >= $syswidth || $j <= 1 || $j >= $syswidth) {
                    $fields[$i][$j] = 0;
                }
            }
        }

        for ($i = 0; $i < $stars; $i++) {
            for ($k = 1; $k <= $r[self::STAR][$i][self::WIDTH]; $k++) {
                for ($l = 1; $l <= $r[self::STAR][$i][self::WIDTH]; $l++) {
                    $xfadd = "" . $k;
                    $yfadd = "" . $l;
                    if ($k < 10) {
                        $xfadd = "0" . $xfadd;
                    }
                    if ($l < 10) {
                        $yfadd = "0" . $yfadd;
                    }

                    $fields[$k + $r[self::STAR][$i][self::X] - 1 + $borderadd][$l + $r[self::STAR][$i][self::Y] - 1 + $borderadd] = $star[$i][self::TYPE] . $xfadd . $yfadd;
                }
            }
        }

        $min = 1000;
        $max = -1000;

        for ($i = 1; $i <= $syswidth; $i++) {
            for ($j = 1; $j <= $syswidth; $j++) {
                if ($fields[$i][$j] == 1) {
                    $d = round($this->distance($i, $j, $m, $m));
                    $min = min($d, $min);
                    $max = max($d, $max);
                    $fields[$i][$j] = $d;
                }
            }
        }

        for ($i = 1; $i <= $syswidth; $i++) {
            for ($j = 1; $j <= $syswidth; $j++) {
                if (($fields[$i][$j] >= 0) && ($fields[$i][$j] <= 30)) {
                    $d = round($this->distance($i, $j, $m, $m));
                    $z = 1;
                    if (($d - $min) > $zone[1] / 100 * ($max - $min)) {
                        $z++;
                    }
                    if (($d - $min) > ($zone[1] + $zone[2]) / 100 * ($max - $min)) {
                        $z++;
                    }
                    if (($d - $min) > ($zone[1] + $zone[2] + $zone[3]) / 100 * ($max - $min)) {
                        $z++;
                    }
                    $zones[$i][$j] = $z;
                    if ($fields[$i][$j] > $max - 1) {
                        $fields[$i][$j] = 0;
                    }
                }
            }
        }
        $min++;
        $max--;

        $beltmax = $fields[1][round($syswidth / 2)];
        $beltmin = $min;

        $m = ceil($syswidth / 2);

        for ($b = 1; $b <= $belts; $b++) {
            list($bphases, $bphase, $thick, $thin, $density, $degradation, $fragmentation, $invert, $isthick) = require_once 'incBelts/' . $belt[$b] . '.php';

            $foo = 0;
            for ($i = 3; $i <= $m; $i++) {
                if (($fields[$i][$m] > 0) && ($fields[$i][$m] < 40)) {
                    $bar[$foo] = $fields[$i][$m];
                    $foo++;
                }
            }
            $beltinner = $bar[rand(0, $foo - 1)];

            if ($isthick == 1) {

                $bphase[1][self::FROM] = array("0" => $beltinner);
                $bphase[1][self::TO] = array("0" => (600 + 2 * $b));
                $bphase[1][self::NUM] = 200;

                $bphase[2][self::FROM] = array("0" => ($beltinner + 1));
                $bphase[2][self::TO] = array("0" => (601 + 2 * $b));
                $bphase[2][self::NUM] = 200;

                for ($p = 1; $p <= $bphases; $p++) {
                    $fields = $this->doPhase($p, $bphase, $fields);
                }

                $thincount = 0;
                $thickcount = 0;
                for ($i = 1; $i <= $syswidth; $i++) {
                    for ($j = 1; $j <= $syswidth; $j++) {
                        if ($fields[$i][$j] == 600 + 2 * $b) {
                            $thickcount++;
                        }
                        if ($fields[$i][$j] == 601 + 2 * $b) {
                            $thincount++;
                        }
                    }
                }

                $thickcount = ceil($thickcount * ($density / 100));
                $thincount = ceil($thincount * ($density / (100 * $degradation)));

                $aphase[0][self::MODE] = "nocluster";
                $aphase[0][self::DESCRIPTION] = "Asteroid";
                $aphase[0][self::NUM] = $thickcount;
                if ($isthick == 1) {
                    $aphase[0][self::FROM] = array("0" => ($invert + 600 + 2 * $b), "1" => ($thin));
                    $aphase[0][self::TO] = array("0" => $thin, "1" => $thick);
                } else {
                    $aphase[0][self::FROM] = array("0" => ($invert + 600 + 2 * $b));
                    $aphase[0][self::TO] = array("0" => $thin);
                }
                $aphase[0][self::ADJACENT] = 0;
                $aphase[0][self::NOADJACENT] = 0;
                $aphase[0][self::NOADJACENTLIMIT] = 0;
                $aphase[0][self::FRAGMENTATION] = $fragmentation;

                $fields = $this->doPhase(0, $aphase, $fields);

                $aphase[0][self::MODE] = "normal";
                $aphase[0][self::DESCRIPTION] = "Asteroid";
                $aphase[0][self::NUM] = $thincount;
                $aphase[0][self::FROM] = array("0" => (601 - $invert + 2 * $b));
                $aphase[0][self::TO] = array("0" => $thin);
                $aphase[0][self::ADJACENT] = 0;
                $aphase[0][self::NOADJACENT] = 0;
                $aphase[0][self::NOADJACENTLIMIT] = 0;
                $aphase[0][self::FRAGMENTATION] = $fragmentation;

                $fields = $this->doPhase(0, $aphase, $fields);
            } else {

                $bphase[1][self::FROM] = array("0" => $beltinner);
                $bphase[1][self::TO] = array("0" => (600 + 2 * $b));
                $bphase[1][self::NUM] = 200;

                $bphase[2][self::FROM] = array("0" => ($beltinner + 1));
                $bphase[2][self::TO] = array("0" => (601 + 2 * $b));
                $bphase[2][self::NUM] = 200;

                for ($p = 1; $p <= $bphases; $p++) {
                    $fields = $this->doPhase($p, $bphase, $fields);
                }

                $thincount = 0;
                $thickcount = 0;
                for ($i = 1; $i <= $syswidth; $i++) {
                    for ($j = 1; $j <= $syswidth; $j++) {
                        if ($fields[$i][$j] == 600 + 2 * $b) {
                            $thickcount++;
                        }
                        if ($fields[$i][$j] == 601 + 2 * $b) {
                            $thincount++;
                        }
                    }
                }

                $thickcount = ceil($thickcount * ($density / 100));
                $thincount = ceil($thincount * ($density / 100));

                $aphase[0][self::MODE] = "normal";
                $aphase[0][self::DESCRIPTION] = "Asteroid";
                $aphase[0][self::NUM] = $thincount + $thickcount;
                $aphase[0][self::FROM] = array("0" => (601 + 2 * $b), "1" => (600 + 2 * $b));
                $aphase[0][self::TO] = array("0" => $thin, "1" => $thin);
                $aphase[0][self::ADJACENT] = 0;
                $aphase[0][self::NOADJACENT] = 0;
                $aphase[0][self::NOADJACENTLIMIT] = 0;
                $aphase[0][self::FRAGMENTATION] = $fragmentation;

                $fields = $this->doPhase(0, $aphase, $fields);
            }
        }

        for ($i = 1; $i <= $syswidth; $i++) {
            for ($j = 1; $j <= $syswidth; $j++) {
                if (($fields[$i][$j] >= 600) && ($fields[$i][$j] <= 699)) {
                    $fields[$i][$j] = 50;
                }
                if (($fields[$i][$j] >= 700) && ($fields[$i][$j] <= 799)) {
                    if (($fields[$i + 1][$j + 1] >= 1) && ($fields[$i + 1][$j + 1] <= 30)) {
                        $fields[$i + 1][$j + 1] = 50;
                    }
                    if (($fields[$i + 1][$j] >= 1) && ($fields[$i + 1][$j] <= 30)) {
                        $fields[$i + 1][$j] = 50;
                    }
                    if (($fields[$i - 1][$j] >= 1) && ($fields[$i - 1][$j] <= 30)) {
                        $fields[$i - 1][$j] = 50;
                    }
                    if (($fields[$i][$j + 1] >= 1) && ($fields[$i][$j + 1] <= 30)) {
                        $fields[$i][$j + 1] = 50;
                    }
                    if (($fields[$i][$j - 1] >= 1) && ($fields[$i][$j - 1] <= 30)) {
                        $fields[$i][$j - 1] = 50;
                    }
                    if (($fields[$i + 1][$j - 1] >= 1) && ($fields[$i + 1][$j - 1] <= 30)) {
                        $fields[$i + 1][$j - 1] = 50;
                    }
                    if (($fields[$i - 1][$j - 1] >= 1) && ($fields[$i - 1][$j - 1] <= 30)) {
                        $fields[$i - 1][$j - 1] = 50;
                    }
                    if (($fields[$i - 1][$j + 1] >= 1) && ($fields[$i - 1][$j + 1] <= 30)) {
                        $fields[$i - 1][$j + 1] = 50;
                    }
                }
            }
        }


        for ($i = 1; $i <= $syswidth; $i++) {
            for ($j = 1; $j <= $syswidth; $j++) {
                if (($fields[$i][$j] != 0) && ($fields[$i][$j] <= 30)) {
                    if ($zones[$i][$j] == 1) {
                        $t = $this->pdraw(array(111 => 45, 115 => 45, 105 => 10));
                    }
                    if ($zones[$i][$j] == 2) {
                        $t = $this->pdraw(array(101 => 40, 103 => 35, 105 => 25));
                    }
                    if ($zones[$i][$j] == 3) {
                        $t = $this->pdraw(array(113 => 60, 107 => 40));
                    }
                    if ($zones[$i][$j] == 4) {
                        $t = 113;
                    }
                    $fields[$i][$j] = $t;
                }
            }
        }

        $planarr = array();
        $sysviewarr = array();

        for ($o = 1; $o <= $data[self::PLANETS]; $o++) {
            // planet phase
            $spphase[0][self::MODE] = "nocluster";
            $spphase[0][self::DESCRIPTION] = "P";
            $spphase[0][self::NUM] = 1;
            $spphase[0][self::FROM] = array(
                "0" => "101",
                "1" => "103",
                "2" => "105",
                "3" => "111",
                "4" => "113",
                "5" => "115",
                "6" => "107"
            );
            $spphase[0][self::TO] = array(
                "0" => "201",
                "1" => "203",
                "2" => "205",
                "3" => "211",
                "4" => "213",
                "5" => "215",
                "6" => "207"
            );
            $spphase[0][self::ADJACENT] = 0;
            $spphase[0][self::NOADJACENT] = 0;
            $spphase[0][self::NOADJACENTLIMIT] = 0;
            $spphase[0][self::FRAGMENTATION] = 1000;

            // moon phase
            $smphase[0][self::MODE] = "nocluster";
            $smphase[0][self::DESCRIPTION] = "M";
            $smphase[0][self::NUM] = 1;
            $smphase[0][self::FROM] = array("0" => "403", "1" => "430");
            $smphase[0][self::TO] = array("0" => "303", "1" => "330");
            $smphase[0][self::ADJACENT] = 0;
            $smphase[0][self::NOADJACENT] = 0;
            $smphase[0][self::NOADJACENTLIMIT] = 0;
            $smphase[0][self::FRAGMENTATION] = 1000;

            unset($mphase, $mphases, $pl);

            $fields = $this->doPhase(0, $spphase, $fields);

            $px = $fields[self::AFFX];
            $py = $fields[self::AFFY];

            $plantype = $fields[$px][$py];

            if (intval($px) == 0 || intval($py) == 0) {
                break;
            }

            $mooncount = 0;
            $moonradius = 2;

            for ($i = 1; $i <= $syswidth; $i++) {
                for ($j = 1; $j <= $syswidth; $j++) {
                    if (($fields[$i][$j] >= 100) && ($fields[$i][$j] <= 200) && (floor($this->distance(
                        $i,
                        $j,
                        $px,
                        $py
                    )) <= $moonradius + 2)) {
                        $fields[$i][$j] = 50;
                    }
                    if (($fields[$i][$j] == 50) && (ceil($this->distance($i, $j, $px, $py)) <= $moonradius)) {
                        $fields[$i][$j] = 60;
                        $mooncount++;
                    }
                }
            }
            // mooncount
            list($moons, $mphases, $mphase) = require_once 'incPlanets/' . $plantype . '.php';

            $pl[self::X] = $px;
            $pl[self::Y] = $py;
            $pl[self::NAME] = "Planet";
            $pl[self::TYPE] = $plantype;

            array_push($planarr, $pl);

            $pl[self::MOONS] = array();

            for ($i = 0; $i < $mphases; $i++) {
                $fields = $this->doPhase($i, $mphase, $fields);
            }

            for ($i = 0; $i < $moons; $i++) {
                unset($mo);
                $fields = $this->doPhase(0, $smphase, $fields);
                if (intval($fields[self::AFFX]) == 0 || intval($fields[self::AFFY]) == 0) {
                    break;
                }

                $mo[self::X] = $fields[self::AFFX];
                $mo[self::Y] = $fields[self::AFFY];
                $mo[self::NAME] = "Mond";
                $mo[self::TYPE] = $fields[$fields[self::AFFX]][$fields[self::AFFY]];

                array_push($planarr, $mo);
                array_push($pl[self::MOONS], $mo);
            }

            array_push($sysviewarr, $pl);

            for ($i = 1; $i <= $syswidth; $i++) {
                for ($j = 1; $j <= $syswidth; $j++) {
                    if ($fields[$i][$j] == 60) {
                        $fields[$i][$j] = 0;
                    }
                    if (($fields[$i][$j] >= 400) && ($fields[$i][$j] <= 499)) {
                        $fields[$i][$j] = 0;
                    }
                }
            }
        }


        for ($i = 1; $i <= $syswidth; $i++) {
            for ($j = 1; $j <= $syswidth; $j++) {
                if ($fields[$i][$j] == 0) {
                    $fields[$i][$j] = 1;
                }
                if ($fields[$i][$j] == 50) {
                    $fields[$i][$j] = 1;
                }
                if (($fields[$i][$j] >= 100) && ($fields[$i][$j] <= 199)) {
                    $fields[$i][$j] = 1;
                }
            }
        }


        $fields[self::WIDTH] = $syswidth;

        for ($iy = 1; $iy <= $syswidth; $iy++) {
            for ($ix = 1; $ix <= $syswidth; $ix++) {
                echo "<img src=\"assets/map/" . $fields[$ix][$iy] . ".gif\" />";
            }
            echo "<br />";
        }
    }
}
