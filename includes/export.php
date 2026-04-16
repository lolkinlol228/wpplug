<?php
if (!defined('ABSPATH')) exit;

/**
 * Native XLSX generator — strict OOXML compliance.
 * 
 * OOXML worksheet element order (MUST be exactly this):
 *   sheetPr → dimension → sheetViews → cols → sheetData → mergeCells → pageMargins → pageSetup
 *
 * Uses PHP built-in ZipArchive only — no external libraries.
 */

function tabel_dispatch_export($route) {
    // Check export permission
    $user = tabel_current_user();
    if (!$user || (empty($user['is_superadmin']) && empty($_SESSION['tabel_perms']['can_export_excel']))) {
        http_response_code(403); echo 'No permission'; return;
    }
    if (preg_match('/^full\/(\d+)\/(\d+)$/', $route, $m)) { tabel_export_generic('full', (int)$m[1], (int)$m[2]); return; }
    if (preg_match('/^brief\/(\d+)\/(\d+)$/', $route, $m)) { tabel_export_generic('brief', (int)$m[1], (int)$m[2]); return; }
    if (preg_match('/^employee\/(\d+)\/(\d+)\/(\d+)$/', $route, $m)) { tabel_export_generic('employee', (int)$m[2], (int)$m[3], (int)$m[1]); return; }
    if ($route === 'backup_zip') { tabel_export_backup_zip(); return; }
    if ($route === 'experience_excel') { tabel_export_experience_excel(); return; }
    if (preg_match('/^stats\/(\d+)\/(\d+)$/', $route, $m)) { tabel_export_generic('stats', (int)$m[1], (int)$m[2]); return; }
    http_response_code(404); echo 'Not found';
}

/* ════════════════════════════════════════════
 *  TabelXlsx — Minimal OOXML-compliant builder
 * ════════════════════════════════════════════ */
class TabelXlsx {
    private $sheets = [];
    private $sheetOrder = [];
    private $cur = null;
    private $strings = [];
    private $strIdx = [];

    /* Style constants */
    const S_DEFAULT  = 0;
    const S_BOLD     = 1;
    const S_TH       = 2;   // header: blue bg, white text, centered, bordered
    const S_TH_SUN   = 3;   // header sunday: red bg
    const S_TD       = 4;   // cell: bordered, centered, 7pt
    const S_TD_L     = 5;   // cell: bordered, left-aligned
    const S_TD_NUM   = 6;   // cell: bordered, centered, number format 0.00
    const S_WORK     = 7;   // green bg
    const S_DAYOFF   = 8;   // pink bg
    const S_HOLIDAY  = 9;   // orange bg
    const S_SICK     = 10;  // light-blue bg
    const S_SUM      = 11;  // gray bg, bold
    const S_TITLE    = 12;  // 12pt bold blue, no border
    const S_BOLD_L   = 13;  // bold left, no border
    const S_BOLD_C   = 14;  // bold 10pt centered, no border
    const S_SM       = 15;  // 7pt centered bordered
    const S_SM_L     = 16;  // 7pt left bordered
    const S_SM_NUM   = 17;  // 7pt num bordered
    const S_GRAY     = 18;  // gray bg for special statuses
    const S_NORM_C   = 19;  // normal center no-border
    const S_VACATION = 20;
    const S_TRIP     = 21;
    const S_MATERNITY= 22;
    const S_CHILDCARE= 23;
    const S_STUDY    = 24;
    const S_ADMIN    = 25;
    const S_ABSENCE  = 26;
    const S_LATE     = 27;
    const S_EARLY    = 28;

    /* ── Sheet management ── */
    function addSheet($name, $landscape = true) {
        $name = mb_substr(preg_replace('/[\[\]\*\?\/\\\\:]/', '', $name), 0, 31);
        if (!$name) $name = 'Sheet' . (count($this->sheetOrder) + 1);
        $this->cur = $name;
        $this->sheets[$name] = [
            'rows' => [], 'merges' => [], 'colW' => [],
            'landscape' => $landscape, 'fitToPage' => true,
        ];
        $this->sheetOrder[] = $name;
        return $this;
    }

    function setColWidths($w) { $this->sheets[$this->cur]['colW'] = $w; return $this; }
    function setPortrait()    { $this->sheets[$this->cur]['landscape'] = false; return $this; }

    function addRow($cells) {
        $this->sheets[$this->cur]['rows'][] = $cells;
        return $this;
    }

    /* Merge: will merge $span columns starting at $col on the NEXT row to be added */
    function mergeNext($col, $span) {
        $r = count($this->sheets[$this->cur]['rows']); // 0-based row index of next row
        $this->sheets[$this->cur]['merges'][] = [$r, $col, $span];
        return $this;
    }

    /* Cell helpers */
    function c($v, $s = self::S_TD)     { return ['v' => (string)$v, 's' => $s]; }
    function n($v, $s = self::S_TD_NUM) { return ['v' => round((float)$v, 4), 's' => $s, 't' => 'n']; }

    /* ── Save to file ── */
    function save($path) {
        if (!class_exists('ZipArchive')) return false;
        
        // Build shared strings FIRST by scanning all cells
        $this->strings = [];
        $this->strIdx  = [];
        foreach ($this->sheets as &$sh) {
            foreach ($sh['rows'] as &$row) {
                foreach ($row as &$cell) {
                    if (is_array($cell) && (!isset($cell['t']) || $cell['t'] !== 'n')) {
                        $cell['_si'] = $this->_addStr((string)$cell['v']);
                    }
                }
            }
        }
        unset($sh, $row, $cell);

        $z = new \ZipArchive();
        if ($z->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) return false;

        $z->addFromString('[Content_Types].xml',         $this->_contentTypes());
        $z->addFromString('_rels/.rels',                 $this->_rootRels());
        $z->addFromString('xl/workbook.xml',             $this->_workbook());
        $z->addFromString('xl/_rels/workbook.xml.rels',  $this->_wbRels());
        $z->addFromString('xl/styles.xml',               $this->_styles());
        $z->addFromString('xl/sharedStrings.xml',        $this->_sharedStrings());

        foreach ($this->sheetOrder as $i => $name) {
            $z->addFromString('xl/worksheets/sheet' . ($i+1) . '.xml', $this->_sheetXml($name, $i === 0));
        }
        return $z->close();
    }

    /* ── Shared string index ── */
    private function _addStr($s) {
        if (isset($this->strIdx[$s])) return $this->strIdx[$s];
        $idx = count($this->strings);
        $this->strings[] = $s;
        $this->strIdx[$s] = $idx;
        return $idx;
    }

    private function _col($c) {
        $l = ''; $c++;
        while ($c > 0) { $c--; $l = chr(65 + $c % 26) . $l; $c = intval($c / 26); }
        return $l;
    }

    /* ── WORKSHEET XML (strict OOXML order) ── */
    private function _sheetXml($name, $isFirst = false) {
        $sh = $this->sheets[$name];
        $rowCount = count($sh['rows']);
        $maxCol = 0;
        foreach ($sh['rows'] as $r) { if (count($r) > $maxCol) $maxCol = count($r); }

        $orient = $sh['landscape'] ? 'landscape' : 'portrait';
        $dim = 'A1:' . $this->_col(max(0, $maxCol - 1)) . max(1, $rowCount);

        $x  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $x .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        /* 1. sheetPr */
        if ($sh['fitToPage']) {
            $x .= '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>';
        }

        /* 2. dimension */
        $x .= '<dimension ref="' . $dim . '"/>';

        /* 3. sheetViews */
        $x .= '<sheetViews><sheetView' . ($isFirst ? ' tabSelected="1"' : '') . ' workbookViewId="0"/></sheetViews>';

        /* 4. sheetFormatPr */
        $x .= '<sheetFormatPr defaultRowHeight="15"/>';

        /* 5. cols */
        if (!empty($sh['colW'])) {
            $x .= '<cols>';
            foreach ($sh['colW'] as $i => $w) {
                $c = $i + 1;
                $x .= '<col min="' . $c . '" max="' . $c . '" width="' . $w . '" customWidth="1"/>';
            }
            $x .= '</cols>';
        }

        /* 6. sheetData */
        $x .= '<sheetData>';
        foreach ($sh['rows'] as $ri => $row) {
            $rn = $ri + 1;
            $x .= '<row r="' . $rn . '">';
            foreach ($row as $ci => $cell) {
                $ref = $this->_col($ci) . $rn;
                if ($cell === null || $cell === '') {
                    $x .= '<c r="' . $ref . '"/>';
                    continue;
                }
                if (!is_array($cell)) $cell = ['v' => $cell, 's' => 0];
                $s = $cell['s'] ?? 0;
                if (isset($cell['t']) && $cell['t'] === 'n') {
                    $x .= '<c r="' . $ref . '" s="' . $s . '"><v>' . $cell['v'] . '</v></c>';
                } else {
                    $si = $cell['_si'] ?? 0;
                    $x .= '<c r="' . $ref . '" s="' . $s . '" t="s"><v>' . $si . '</v></c>';
                }
            }
            $x .= '</row>';
        }
        $x .= '</sheetData>';

        /* 7. mergeCells */
        if (!empty($sh['merges'])) {
            $x .= '<mergeCells count="' . count($sh['merges']) . '">';
            foreach ($sh['merges'] as $m) {
                $r = $m[0] + 1;
                $from = $this->_col($m[1]) . $r;
                $to   = $this->_col($m[1] + $m[2] - 1) . $r;
                $x .= '<mergeCell ref="' . $from . ':' . $to . '"/>';
            }
            $x .= '</mergeCells>';
        }

        /* 8. pageMargins */
        $x .= '<pageMargins left="0.15" right="0.15" top="1.18" bottom="0.2" header="0" footer="0"/>';

        /* 9. pageSetup */
        $x .= '<pageSetup paperSize="9" orientation="' . $orient . '" fitToWidth="1" fitToHeight="1"/>';

        $x .= '</worksheet>';
        return $x;
    }

    /* ── Shared Strings ── */
    private function _sharedStrings() {
        $n = count($this->strings);
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $n . '" uniqueCount="' . $n . '">';
        foreach ($this->strings as $s) {
            // Clean any characters illegal in XML 1.0
            $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $s);
            $x .= '<si><t>' . htmlspecialchars($s, ENT_XML1, 'UTF-8') . '</t></si>';
        }
        $x .= '</sst>';
        return $x;
    }

    /* ── Styles ── */
    private function _styles() {
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" mc:Ignorable="x14ac"'
            . ' xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac">';

        /* numFmts */
        $x .= '<numFmts count="1"><numFmt numFmtId="164" formatCode="0.00"/></numFmts>';

        /* fonts: 0=9pt, 1=9pt bold, 2=7pt white bold, 3=12pt bold blue, 4=7pt, 5=10pt bold */
        $x .= '<fonts count="6" x14ac:knownFonts="1">';
        $x .= '<font><sz val="9"/><name val="Arial"/></font>';
        $x .= '<font><b/><sz val="9"/><name val="Arial"/></font>';
        $x .= '<font><b/><sz val="7"/><color rgb="FFFFFFFF"/><name val="Arial"/></font>';
        $x .= '<font><b/><sz val="12"/><color rgb="FF2B5797"/><name val="Arial"/></font>';
        $x .= '<font><sz val="7"/><name val="Arial"/></font>';
        $x .= '<font><b/><sz val="12"/><name val="Arial"/></font>';
        $x .= '</fonts>';

        /* fills: 0=none 1=gray125 2=blue 3=red 4=green 5=pink 6=orange 7=ltblue 8=ltgray 9=gray
                  10=purple(vacation) 11=dkorange(trip) 12=hotpink(maternity) 13=deeporange(child)
                  14=bluegray(study) 15=brown(admin) 16=red(absence) 17=amber(late) 18=dkred(early) */
        $x .= '<fills count="19">';
        $x .= '<fill><patternFill patternType="none"/></fill>';
        $x .= '<fill><patternFill patternType="gray125"/></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FF2B5797"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFC62828"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFE8F5E9"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFFD9D9"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFFE0B2"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFB3E5FC"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFF0F0F0"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFE0E0E0"/><bgColor indexed="64"/></patternFill></fill>';
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFF3E5F5"/><bgColor indexed="64"/></patternFill></fill>'; // 10 purple
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFFF3E0"/><bgColor indexed="64"/></patternFill></fill>'; // 11 lt orange
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFCE4EC"/><bgColor indexed="64"/></patternFill></fill>'; // 12 hot pink
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFBE9E7"/><bgColor indexed="64"/></patternFill></fill>'; // 13 deep orange
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFECEFF1"/><bgColor indexed="64"/></patternFill></fill>'; // 14 blue gray
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFEFEBE9"/><bgColor indexed="64"/></patternFill></fill>'; // 15 brown
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFFEBEE"/><bgColor indexed="64"/></patternFill></fill>'; // 16 red
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFFF8E1"/><bgColor indexed="64"/></patternFill></fill>'; // 17 amber
        $x .= '<fill><patternFill patternType="solid"><fgColor rgb="FFFBE9E7"/><bgColor indexed="64"/></patternFill></fill>'; // 18 dk red
        $x .= '</fills>';

        /* borders: 0=none 1=thin-all */
        $x .= '<borders count="2">';
        $x .= '<border><left/><right/><top/><bottom/><diagonal/></border>';
        $x .= '<border>'
            . '<left style="thin"><color auto="1"/></left>'
            . '<right style="thin"><color auto="1"/></right>'
            . '<top style="thin"><color auto="1"/></top>'
            . '<bottom style="thin"><color auto="1"/></bottom>'
            . '<diagonal/></border>';
        $x .= '</borders>';

        $x .= '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>';

        /*
         * cellXfs — index matches S_* constants
         *  0  DEFAULT:    font0, no border, wrap
         *  1  BOLD:       font1, no border
         *  2  TH:         font2(white), fill2(blue), border1, center
         *  3  TH_SUN:     font2(white), fill3(red),  border1, center
         *  4  TD:         font4(7pt), border1, center
         *  5  TD_L:       font4, border1, left
         *  6  TD_NUM:     font4, border1, center, numFmt 164
         *  7  WORK:       font4, fill4(green), border1, center
         *  8  DAYOFF:     font4, fill5(pink),  border1, center
         *  9  HOLIDAY:    font4, fill6(orange), border1, center
         * 10  SICK:       font4, fill7(ltblue), border1, center
         * 11  SUM:        font1(bold), fill8(ltgray), border1, center
         * 12  TITLE:      font3(12pt blue), no border, center
         * 13  BOLD_L:     font1, no border, left
         * 14  BOLD_C:     font5(10pt), no border, center
         * 15  SM:         font4, border1, center (same as TD)
         * 16  SM_L:       font4, border1, left (same as TD_L)
         * 17  SM_NUM:     font4, border1, numFmt 164 (same as TD_NUM)
         * 18  GRAY:       font4, fill9(gray), border1, center
         */
        $x .= '<cellXfs count="30">';
        $a_cw = ' horizontal="center" vertical="center" wrapText="1"';
        $a_c  = ' horizontal="center" vertical="center"';
        $a_l  = ' horizontal="left" vertical="center" wrapText="1"';
        $a_lw = ' horizontal="left" vertical="center" wrapText="1"';
        $aa = ' applyAlignment="1"';
        $b = ' applyBorder="1"'; $f = ' applyFill="1"'; $fn = ' applyFont="1"'; $nf = ' applyNumberFormat="1"';

        $x .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"' . $aa . '><alignment vertical="center" wrapText="1"/></xf>';  // 0
        $x .= '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"' . $fn . $aa . '><alignment vertical="center"/></xf>';   // 1
        $x .= '<xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_cw . '/></xf>'; // 2
        $x .= '<xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_cw . '/></xf>'; // 3
        $x .= '<xf numFmtId="0" fontId="4" fillId="0" borderId="1" xfId="0"' . $fn . $b . $aa . '><alignment' . $a_c . '/></xf>';       // 4
        $x .= '<xf numFmtId="0" fontId="4" fillId="0" borderId="1" xfId="0"' . $fn . $b . $aa . '><alignment' . $a_lw . '/></xf>';      // 5
        $x .= '<xf numFmtId="164" fontId="4" fillId="0" borderId="1" xfId="0"' . $nf . $fn . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 6
        $x .= '<xf numFmtId="0" fontId="4" fillId="4" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 7 work green
        $x .= '<xf numFmtId="0" fontId="4" fillId="5" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 8 dayoff pink
        $x .= '<xf numFmtId="0" fontId="4" fillId="6" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 9 holiday orange
        $x .= '<xf numFmtId="0" fontId="4" fillId="7" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 10 sick blue
        $x .= '<xf numFmtId="0" fontId="1" fillId="8" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 11 sum gray
        $x .= '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0"' . $fn . $aa . '><alignment' . $a_c . '/></xf>';           // 12 title
        $x .= '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"' . $fn . $aa . '><alignment' . $a_l . '/></xf>';           // 13 bold left
        $x .= '<xf numFmtId="0" fontId="5" fillId="0" borderId="0" xfId="0"' . $fn . $aa . '><alignment' . $a_c . '/></xf>';           // 14 bold center
        $x .= '<xf numFmtId="0" fontId="4" fillId="0" borderId="1" xfId="0"' . $fn . $b . $aa . '><alignment' . $a_c . '/></xf>';      // 15
        $x .= '<xf numFmtId="0" fontId="4" fillId="0" borderId="1" xfId="0"' . $fn . $b . $aa . '><alignment' . $a_l . '/></xf>';      // 16
        $x .= '<xf numFmtId="164" fontId="4" fillId="0" borderId="1" xfId="0"' . $nf . $fn . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 17
        $x .= '<xf numFmtId="0" fontId="4" fillId="9" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 18 gray
        $x .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"' . $aa . '><alignment horizontal="center" vertical="center"/></xf>'; // 19 normal center
        // Mark-specific colored styles: 20-28
        $x .= '<xf numFmtId="0" fontId="4" fillId="10" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 20 vacation purple
        $x .= '<xf numFmtId="0" fontId="4" fillId="11" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 21 business_trip lt orange
        $x .= '<xf numFmtId="0" fontId="4" fillId="12" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 22 maternity hot pink
        $x .= '<xf numFmtId="0" fontId="4" fillId="13" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 23 childcare deep orange
        $x .= '<xf numFmtId="0" fontId="4" fillId="14" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 24 study_leave blue gray
        $x .= '<xf numFmtId="0" fontId="4" fillId="15" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 25 admin_leave brown
        $x .= '<xf numFmtId="0" fontId="4" fillId="16" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 26 absence red
        $x .= '<xf numFmtId="0" fontId="4" fillId="17" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 27 late amber
        $x .= '<xf numFmtId="0" fontId="4" fillId="18" borderId="1" xfId="0"' . $fn . $f . $b . $aa . '><alignment' . $a_c . '/></xf>'; // 28 early_leave dk red
        $x .= '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"' . $aa . '><alignment horizontal="left" vertical="center"/></xf>'; // 29 normal left no border
        $x .= '</cellXfs>';

        $x .= '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>';
        $x .= '<dxfs count="0"/><tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/>';
        $x .= '</styleSheet>';
        return $x;
    }

    /* ── Package files ── */
    private function _contentTypes() {
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $x .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $x .= '<Default Extension="xml" ContentType="application/xml"/>';
        $x .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
        $x .= '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        $x .= '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>';
        foreach ($this->sheetOrder as $i => $n)
            $x .= '<Override PartName="/xl/worksheets/sheet' . ($i+1) . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        $x .= '</Types>';
        return $x;
    }

    private function _rootRels() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function _workbook() {
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $x .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
        $x .= '<sheets>';
        foreach ($this->sheetOrder as $i => $name)
            $x .= '<sheet name="' . htmlspecialchars($name, ENT_XML1, 'UTF-8') . '" sheetId="' . ($i+1) . '" r:id="rId' . ($i+1) . '"/>';
        $x .= '</sheets></workbook>';
        return $x;
    }

    private function _wbRels() {
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
           . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        foreach ($this->sheetOrder as $i => $n)
            $x .= '<Relationship Id="rId' . ($i+1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . ($i+1) . '.xml"/>';
        $rid = count($this->sheetOrder) + 1;
        $x .= '<Relationship Id="rId' . $rid . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
        $x .= '<Relationship Id="rId' . ($rid+1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';
        $x .= '</Relationships>';
        return $x;
    }
}

/* ═══════ Helpers ═══════ */
function _emp_calc($emp, $yr, $mo, $db, $dbtype, $wpdb, $ms, $te, $days) {
    $fired = $wpdb->get_var($wpdb->prepare("SELECT is_fired FROM $ms WHERE db_name=%s AND employee_id=%d AND year=%d AND month=%d AND is_fired=1", $db, $emp['id'], $yr, $mo));
    if ($fired) return null;
    $ed = tabel_get_employee_for_month($emp, $yr, $mo, $db);
    foreach (['employment_internal','employment_external','pedagog_experience','actual_hours','position_id'] as $f)
        if (!isset($ed[$f]) || $ed[$f] === null) $ed[$f] = $emp[$f] ?? null;
    $entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM $te WHERE db_name=%s AND employee_id=%d AND year=%d AND month=%d", $db, $emp['id'], $yr, $mo), ARRAY_A);
    $edict = []; foreach ($entries as $e) $edict[(int)$e['day']] = $e;
    $calc = tabel_calc_employee_month($ed, $edict, $days, $dbtype);
    $cond = '';
    if (($ed['employment_internal'] ?? '') === 'staff') $cond = 'Штатный';
    elseif (($ed['employment_internal'] ?? '') === 'part_time') $cond = 'Совм. внутр.';
    elseif (($ed['employment_external'] ?? '') === 'part_time') $cond = 'Совм. внеш.';
    return ['ed' => $ed, 'calc' => $calc, 'cond' => $cond];
}

function _ds($status) {
    switch ($status) {
        case 'work': return TabelXlsx::S_WORK;
        case 'day_off': return TabelXlsx::S_DAYOFF;
        case 'holiday': return TabelXlsx::S_HOLIDAY;
        case 'sick': return TabelXlsx::S_SICK;
        case 'vacation': return TabelXlsx::S_VACATION;
        case 'business_trip': return TabelXlsx::S_TRIP;
        case 'maternity': return TabelXlsx::S_MATERNITY;
        case 'childcare': return TabelXlsx::S_CHILDCARE;
        case 'study_leave': return TabelXlsx::S_STUDY;
        case 'admin_leave': return TabelXlsx::S_ADMIN;
        case 'absence': return TabelXlsx::S_ABSENCE;
        case 'late': return TabelXlsx::S_LATE;
        case 'early_leave': return TabelXlsx::S_EARLY;
    }
    return TabelXlsx::S_SM;
}

/* ═══════ Main export ═══════ */
function tabel_export_generic($type, $year, $month, $eid = null) {
    global $wpdb;
    $db_name = tabel_active_db();
    if (!$db_name) { echo 'No DB'; return; }
    $lang = isset($_SESSION['tabel_lang']) ? $_SESSION['tabel_lang'] : 'ru';
    $T = tabel_get_translations($lang);
    $MK = tabel_get_month_keys();
    $db_type = tabel_active_db_type();
    $month_name = $T[$MK[$month-1]];
    $emp_t = tabel_table('employees'); $te_t = tabel_table('timesheet_entries');
    $ms_t = tabel_table('monthly_settings'); $es_t = tabel_table('excel_settings');
    $days_info = tabel_get_month_calendar($year, $month);
    $day_names = tabel_get_day_names($lang);
    $S = [];
    foreach ($wpdb->get_results($wpdb->prepare("SELECT setting_key, setting_value FROM $es_t WHERE db_name=%s", $db_name), ARRAY_A) as $r)
        $S[$r['setting_key']] = $r['setting_value'];
    if ($type === 'employee' && $eid)
        $employees = $wpdb->get_results($wpdb->prepare("SELECT * FROM $emp_t WHERE id=%d AND db_name=%s", $eid, $db_name), ARRAY_A);
    else
        $employees = $wpdb->get_results($wpdb->prepare("SELECT * FROM $emp_t WHERE db_name=%s ORDER BY CASE WHEN pay_type='fixed' OR rate IS NULL OR rate=0 THEN 0 ELSE 1 END, full_name", $db_name), ARRAY_A);

    if ($type === 'full') $fname = "Табель_{$month_name}_{$year}.xlsx";
    elseif ($type === 'brief') $fname = "Табель_{$month_name}_{$year}_кыскача.xlsx";
    elseif ($type === 'employee' && !empty($employees)) $fname = "{$employees[0]['full_name']}_{$month_name}_{$year}.xlsx";
    else $fname = "Статистика_{$month_name}_{$year}.xlsx";

    $x = new TabelXlsx();

    if ($type === 'full')
        tabel_build_full($x, $employees, $days_info, $day_names, $year, $month, $S, $T, $MK, $db_name, $db_type, $wpdb, $ms_t, $te_t);
    elseif ($type === 'brief')
        tabel_build_brief($x, $employees, $days_info, $year, $month, $T, $MK, $db_name, $db_type, $wpdb, $ms_t, $te_t);
    elseif ($type === 'employee')
        tabel_build_employee($x, $employees, $days_info, $day_names, $year, $month, $T, $MK, $db_name, $db_type, $wpdb, $te_t);
    else
        tabel_build_stats($x, $employees, $days_info, $year, $month, $T, $MK, $db_name, $db_type, $wpdb, $ms_t, $te_t);

    $tmp = tempnam(sys_get_temp_dir(), 'tabel_') . '.xlsx';
    if (!$x->save($tmp)) {
        http_response_code(500);
        echo 'Failed to create XLSX file. ZipArchive may not be available.';
        return;
    }
    $content = file_get_contents($tmp);
    @unlink($tmp);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . rawurlencode($fname) . '"');
    header('Content-Length: ' . strlen($content));
    header('Cache-Control: max-age=0');
    echo $content;

    tabel_log_export($type, $year, $month, $content, ($type === 'employee' && !empty($employees)) ? $employees[0]['full_name'] : null);
    tabel_send_telegram_notification($fname, $type, $year, $month, ($type === 'employee' && !empty($employees)) ? $employees[0]['full_name'] : null);
}

/* ═══════ FULL ═══════ */
function tabel_build_full($x, $emps, $days, $dnames, $yr, $mo, $S, $T, $MK, $db, $dbt, $wpdb, $ms, $te) {
    $dc = count($days); $tc = 7 + $dc + 3;
    $kg = ['январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'];
    $g = function($k, $d='') use ($S) { return $S[$k] ?? $d; };

    $x->addSheet($T[$MK[$mo-1]] . ' ' . $yr);
    $w = [4,22,16,11,7,7,7]; for ($i=0;$i<$dc;$i++) $w[]=4.5; array_push($w,7,7,7);
    $x->setColWidths($w);

    $fill = function($tc) { return array_fill(0, $tc, ''); };
    $left_cols = 7;
    $center_cols = max(1, $dc - 6);
    $right_cols = $tc - $left_cols - $center_cols;
    $cs = $left_cols;
    $rs = $left_cols + $center_cols;

    // Row 0: header_left1 (bold left) | header_center1 (bold center) | header_right1 (bold left)
    $r = $fill($tc);
    $r[0] = $x->c($g('header_left1','Макулдашылды'), 13); $r[$cs] = $x->c($g('header_center1',''), 14); $r[$rs] = $x->c($g('header_right1','БЕКИТЕМ'), 13);
    $x->mergeNext(0,$left_cols); $x->mergeNext($cs,$center_cols); $x->mergeNext($rs,$right_cols);
    $x->addRow($r);

    // Row 1
    $r = $fill($tc);
    $r[0] = $x->c($g('header_left2',''), 29); $r[$cs] = $x->c($g('header_center2',''), 14); $r[$rs] = $x->c($g('header_right2',''), 29);
    $x->mergeNext(0,$left_cols); $x->mergeNext($cs,$center_cols); $x->mergeNext($rs,$right_cols);
    $x->addRow($r);

    // Row 2
    $r = $fill($tc);
    $r[0] = $x->c($g('header_left3',''), 29); $r[$cs] = $x->c($g('header_center3',''), 14); $r[$rs] = $x->c($g('header_right3',''), 29);
    $x->mergeNext(0,$left_cols); $x->mergeNext($cs,$center_cols); $x->mergeNext($rs,$right_cols);
    $x->addRow($r);

    // Row 3: dates (left) | document title (center) | dates (left)
    $r = $fill($tc);
    $r[0] = $x->c('"_____" __________ '.$yr.'-ж.', 29);
    $r[$cs] = $x->c(isset($S['doc_title']) ? $S['doc_title'] : 'Профессордук-окутуучулук курамдын жумуш убактысын эсепке алуу', 14);
    $r[$rs] = $x->c('"_____" __________ '.$yr.'-ж.', 29);
    $x->mergeNext(0,$left_cols); $x->mergeNext($cs,$center_cols); $x->mergeNext($rs,$right_cols);
    $x->addRow($r);

    // Row 4: ТАБЕЛИ (center block only)
    $r = $fill($tc); $r[$cs] = $x->c('ТАБЕЛИ', 12);
    $x->mergeNext($cs, $center_cols); $x->addRow($r);

    // Row 5: month-year (center block only)
    $r = $fill($tc); $r[$cs] = $x->c($yr.'-жылдын '.$kg[$mo-1].' айы', 14);
    $x->mergeNext($cs, $center_cols); $x->addRow($r);

    // Row 6: blank
    $x->addRow($fill($tc));

    // Row 7: column headers
    $h = [$x->c('№',2), $x->c($T['full_name'],2), $x->c($T['position'],2), $x->c('Шарттары',2),
          $x->c($T['pedagog_experience']??'Стаж',2), $x->c($T['actual_hours']??'Факт.с.',2), $x->c($T['rate'],2)];
    foreach ($days as $di) $h[] = $x->c($di['day']."\n".$dnames[$di['weekday']], $di['is_sunday']?3:2);
    array_push($h, $x->c($T['working_days'],2), $x->c($T['weekends'],2), $x->c($T['days_in_month'],2));
    $x->addRow($h);

    // Data
    $num = 0; $all_mark_counts = [];
    foreach ($emps as $emp) {
        $rc = _emp_calc($emp, $yr, $mo, $db, $dbt, $wpdb, $ms, $te, $days);
        if (!$rc) continue;
        $ed = $rc['ed']; $c = $rc['calc']; $num++;
        foreach ($c['mark_counts'] as $mk => $cnt)
            $all_mark_counts[$mk] = ($all_mark_counts[$mk] ?? 0) + $cnt;
        $r = [$x->n($num,4), $x->c($ed['full_name'],5), $x->c($ed['position']??'',5), $x->c($rc['cond'],4),
              $x->c($ed['pedagog_experience']??'',4), $x->c($ed['actual_hours']??'',4), $x->n($ed['rate']??0,6)];
        foreach ($c['day_cells'] as $dcell) {
            $s = _ds($dcell['status']); $v = $dcell['display'];
            $r[] = is_numeric($v) ? $x->n($v,$s) : $x->c($v,$s);
        }
        array_push($r, $x->n($c['working_days'],11), $x->n($c['weekends'],11), $x->n(count($days),11));
        $x->addRow($r);
    }

    // Signatures
    $x->addRow($fill($tc));
    foreach (['sig1','sig2','sig3'] as $sk) {
        $r = $fill($tc); $r[0] = $x->c($g($sk), 13);
        $x->mergeNext(0, $tc); $x->addRow($r);
        $x->addRow($fill($tc));
    }

    // ══════ SHEET 2: Шарттуу белгилер ══════
    $mn = $T[$MK[$mo-1]];
    $x->addSheet('Шарттуу белгилер', false);
    $x->setColWidths([10, 55, 10]);
    $x->mergeNext(0,3);
    $x->addRow([$x->c('Шарттуу белгилер / Условные обозначения', 12), '', '']);
    $x->mergeNext(0,3);
    $x->addRow([$x->c($mn.' '.$yr, 14), '', '']);
    $x->addRow(['','','']);
    $x->addRow([$x->c('Белги',2), $x->c('Түшүндүрмөсү / Значение',2), $x->c('Саны',2)]);

    $es_t = tabel_table('excel_settings');
    $mj = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $es_t WHERE db_name=%s AND setting_key='marks_list'", $db));
    $ml = $mj ? json_decode($mj, true) : null;
    if (!is_array($ml)) {
        $ml = [
            ['symbol'=>'К','description'=>'Каникулдар'],['symbol'=>'О','description'=>'Оорулуу'],
            ['symbol'=>'С','description'=>'Иш сапарлар'],['symbol'=>'КӨ','description'=>'Кош бойлуулук жана төрөт өргүү'],
            ['symbol'=>'БӨ','description'=>'Бала багуу боюнча өргүү'],['symbol'=>'ОӨ','description'=>'Окуусуна байланыштуу өргүү'],
            ['symbol'=>'АӨ','description'=>'Администрация тарабынан берилген дем алыш'],
            ['symbol'=>'Дк','description'=>'Дайынсыз ишке келбей калуу'],
            ['symbol'=>'Кк','description'=>'Ишке кечигип келүү'],['symbol'=>'Эк','description'=>'Иштен эрте кетип калуу'],
        ];
    }
    foreach ($ml as $item) {
        $sym = $item['symbol'] ?? '';
        $desc = $item['description'] ?? '';
        $cnt = $all_mark_counts[$sym] ?? 0;
        $x->addRow([$x->c($sym,1), $x->c($desc,5), $x->n($cnt, $cnt>0?9:4)]);
    }
}


/* ═══════ BRIEF ═══════ */
function tabel_build_brief($x, $emps, $days, $yr, $mo, $T, $MK, $db, $dbt, $wpdb, $ms, $te) {
    $x->addSheet('Кыскача');
    $x->setColWidths([4,22,16,11,7,7,8,8,8,7,7,7]);
    $hh = [$T['number']??'№',$T['full_name'],$T['position'],'Шарттары',$T['pedagog_experience']??'Стаж',
           $T['actual_hours']??'Факт.ч.',$T['rate'],$T['working_days'],$T['working_hours'],$T['weekends'],$T['holidays'],$T['days_in_month']];
    $x->addRow(array_map(function($h) use ($x) { return $x->c($h,2); }, $hh));
    $i = 0;
    foreach ($emps as $emp) {
        $rc = _emp_calc($emp, $yr, $mo, $db, $dbt, $wpdb, $ms, $te, $days); if (!$rc) continue;
        $ed = $rc['ed']; $c = $rc['calc']; $i++;
        $x->addRow([$x->n($i,4),$x->c($ed['full_name'],5),$x->c($ed['position']??'',5),$x->c($rc['cond'],4),
            $x->c($ed['pedagog_experience']??'',4),$x->c($ed['actual_hours']??'',4),$x->n($ed['rate']??0,6),
            $x->n($c['working_days'],4),$x->n($c['working_hours'],4),$x->n($c['weekends'],4),$x->n($c['holidays'],4),$x->n(count($days),4)]);
    }
}

/* ═══════ EMPLOYEE ═══════ */
function tabel_build_employee($x, $emps, $days, $dnames, $yr, $mo, $T, $MK, $db, $dbt, $wpdb, $te) {
    if (empty($emps)) return;
    $emp = $emps[0];
    $ed = tabel_get_employee_for_month($emp, $yr, $mo, $db);
    $entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM $te WHERE db_name=%s AND employee_id=%d AND year=%d AND month=%d", $db, $emp['id'], $yr, $mo), ARRAY_A);
    $edict = []; foreach ($entries as $e) $edict[(int)$e['day']] = $e;
    $calc = tabel_calc_employee_month($ed, $edict, $days, $dbt);
    $mn = $T[$MK[$mo-1]];
    $sl = ['work'=>'Р','day_off'=>'В','holiday'=>'П','sick'=>'Б','absent'=>'-','vacation'=>'К','business_trip'=>'С',
           'maternity'=>'КӨ','childcare'=>'БӨ','study_leave'=>'ОӨ','admin_leave'=>'АӨ','absence'=>'Дк','late'=>'Кк','early_leave'=>'Эк'];

    $x->addSheet($ed['full_name']); $x->setColWidths([18,12,12]);
    $x->mergeNext(0,3); $x->addRow([$x->c($ed['full_name'].' — '.$mn.' '.$yr, 12),'','']);
    $x->addRow([$x->c($T['position'],1), $x->c($ed['position']??'',4), '']);
    $x->addRow([$x->c($T['rate'],1), $x->n($ed['rate']??0,6), '']);
    $x->addRow(['','','']);
    $x->addRow([$x->c($T['day'],2), $x->c($T['status'],2), $x->c($T['working_hours'],2)]);
    foreach ($calc['day_cells'] as $dc) {
        $s = _ds($dc['status']); $v = $dc['display'];
        $x->addRow([$x->c($dc['day'].' ('.$dnames[$dc['weekday']].')',$s), $x->c($sl[$dc['status']]??$dc['status'],$s),
            is_numeric($v)?$x->n($v,$s):$x->c($v,$s)]);
    }
    $x->addRow(['','','']);
    $x->mergeNext(0,3); $x->addRow([$x->c($T['summary'],12),'','']);
    foreach ([[$T['working_days'],$calc['working_days']],[$T['working_hours'],$calc['working_hours']],
              [$T['weekends'],$calc['weekends']],[$T['holidays'],$calc['holidays']],[$T['days_in_month'],count($days)]] as $row)
        $x->addRow([$x->c($row[0],1), $x->n($row[1],4), '']);
}

/* ═══════ STATS ═══════ */
function tabel_build_stats($x, $emps, $days, $yr, $mo, $T, $MK, $db, $dbt, $wpdb, $ms, $te) {
    $mn = $T[$MK[$mo-1]];
    $x->addSheet('Статистика'); $x->setColWidths([4,22,16,9,12]);
    $x->mergeNext(0,5); $x->addRow([$x->c('Статистика: '.$mn.' '.$yr, 12),'','','','']);
    $x->addRow([$x->c('№',2),$x->c($T['full_name'],2),$x->c($T['position'],2),$x->c($T['working_days'],2),$x->c($T['total_pay'],2)]);
    $i = 0;
    foreach ($emps as $emp) {
        $rc = _emp_calc($emp, $yr, $mo, $db, $dbt, $wpdb, $ms, $te, $days); if (!$rc) continue;
        $ed = $rc['ed']; $c = $rc['calc']; $i++;
        $x->addRow([$x->n($i,4),$x->c($ed['full_name'],5),$x->c($ed['position']??'',5),$x->n($c['working_days'],4),$x->n($c['total_pay'],6)]);
    }
}

/* ── Telegram ── */
function tabel_send_telegram_notification($fname, $type, $yr, $mo, $ename = null) {
    $tk = get_option('tabel_telegram_token',''); $ch = get_option('tabel_telegram_chat_id','');
    if (!$tk || !$ch) return;
    $mn = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
    $msg = "🔔 Экспорт: {$mn[$mo-1]} {$yr}\n📊 " . $type;
    if ($ename) $msg .= "\n👤 {$ename}";
    @wp_remote_post("https://api.telegram.org/bot{$tk}/sendMessage", ['body'=>['chat_id'=>$ch,'text'=>$msg,'parse_mode'=>'HTML'],'timeout'=>10]);
}
function tabel_export_backup_zip() {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user || empty($user['is_superadmin'])) { http_response_code(403); echo 'Forbidden'; return; }
    $db_filter = isset($_GET['db']) ? sanitize_text_field($_GET['db']) : null;
    $tables = ['employees','positions','timesheet_entries','monthly_settings','excel_settings','experience'];
    $tmp = tempnam(sys_get_temp_dir(), 'tabel_backup_');
    $zip = new ZipArchive();
    if ($zip->open($tmp, ZipArchive::CREATE|ZipArchive::OVERWRITE) !== true) { http_response_code(500); return; }
    foreach ($tables as $tname) { $t = tabel_table($tname); $rows = ($db_filter && $tname!=='experience') ? $wpdb->get_results($wpdb->prepare("SELECT * FROM $t WHERE db_name=%s",$db_filter),ARRAY_A) : $wpdb->get_results("SELECT * FROM $t",ARRAY_A); $zip->addFromString($tname.'.json',json_encode($rows?:[],JSON_UNESCAPED_UNICODE)); }
    $db_t = tabel_table('databases'); $dbs = $db_filter ? $wpdb->get_results($wpdb->prepare("SELECT * FROM $db_t WHERE name=%s",$db_filter),ARRAY_A) : $wpdb->get_results("SELECT * FROM $db_t",ARRAY_A);
    $zip->addFromString('databases.json',json_encode($dbs?:[],JSON_UNESCAPED_UNICODE)); $zip->close();
    $fname='tabel_backup_'.($db_filter?:'all').'_'.date('Y-m-d_His').'.zip';
    header('Content-Type:application/zip'); header('Content-Disposition:attachment;filename="'.$fname.'"'); header('Content-Length:'.filesize($tmp)); readfile($tmp); @unlink($tmp); exit;
}
function tabel_import_backup_zip() {
    global $wpdb; $user=tabel_current_user();
    if(!$user||empty($user['is_superadmin'])){header('Content-Type:application/json');echo json_encode(['ok'=>false,'error'=>'Forbidden']);exit;}
    header('Content-Type:application/json;charset=utf-8');
    if(!isset($_FILES['backup_zip'])||$_FILES['backup_zip']['error']!==UPLOAD_ERR_OK){echo json_encode(['ok'=>false,'error'=>'Файл не загружен']);exit;}
    $mode=isset($_POST['mode'])?$_POST['mode']:'merge'; $db_name=isset($_POST['db_name'])?$_POST['db_name']:null;
    $zip=new ZipArchive(); if($zip->open($_FILES['backup_zip']['tmp_name'])!==true){echo json_encode(['ok'=>false,'error'=>'Невозможно открыть ZIP']);exit;}
    $imported=[];
    foreach(['databases','employees','positions','timesheet_entries','monthly_settings','excel_settings','experience'] as $tname){
        $json=$zip->getFromName($tname.'.json'); if($json===false)continue; $rows=json_decode($json,true); if(!is_array($rows)||empty($rows))continue;
        $t=tabel_table($tname); if($mode==='replace'&&$db_name&&!in_array($tname,['experience','databases']))$wpdb->query($wpdb->prepare("DELETE FROM $t WHERE db_name=%s",$db_name));
        $c=0; foreach($rows as $row){unset($row['id']);if($db_name&&isset($row['db_name'])&&$row['db_name']!==$db_name)continue;$wpdb->insert($t,$row);if($wpdb->insert_id)$c++;} $imported[$tname]=$c;
    } $zip->close(); echo json_encode(['ok'=>true,'imported'=>$imported]); exit;
}
function tabel_export_experience_excel() {
    global $wpdb; $emps=$wpdb->get_results("SELECT id,full_name,db_name FROM ".tabel_table('employees')." ORDER BY full_name",ARRAY_A);
    $x=new TabelXlsx();$x->addSheet('Стаж');$x->setColWidths([4,30,20,15,40]);
    $x->addRow([$x->c('№',2),$x->c('ФИО',2),$x->c('База',2),$x->c('Стаж',2),$x->c('Периоды',2)]);
    $i=0; foreach($emps as $emp){$exp=tabel_calc_experience((int)$emp['id']);$i++;$ps='';
    if(!empty($exp['periods'])){$parts=[];foreach($exp['periods'] as $p){$to=$p['is_current']?'наст.время':($p['date_to']?:'?');$parts[]=$p['date_from'].' — '.$to.($p['note']?' ('.$p['note'].')':'');}$ps=implode('; ',$parts);}
    $x->addRow([$x->n($i,4),$x->c($emp['full_name'],5),$x->c($emp['db_name'],4),$x->c($exp['display']?:'—',4),$x->c($ps,5)]);}
    $content=$x->build(); header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition:attachment;filename="experience_'.date('Y-m-d').'.xlsx"'); header('Content-Length:'.strlen($content)); echo $content; exit;
}
