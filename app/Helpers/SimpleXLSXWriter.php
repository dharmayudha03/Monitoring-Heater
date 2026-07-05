<?php

namespace App\Helpers;

use ZipArchive;

class SimpleXLSXWriter
{
    public static function download(string $filename, array $headers, array $rows, string $title = 'Laporan')
    {
        return self::downloadMultiTable($filename, [
            [
                'section_title' => '',
                'headers' => $headers,
                'rows' => $rows
            ]
        ], $title);
    }

    public static function downloadMultiTable(string $filename, array $sections, string $title = 'Laporan Multi Table')
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // 1. [Content_Types].xml
            $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>');

            // 2. _rels/.rels
            $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

            // 3. xl/_rels/workbook.xml.rels
            $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>');

            // 4. xl/workbook.xml
            $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Laporan Executive" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

            // 5. xl/styles.xml
            $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="3">
    <font><sz val="11"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>
    <font><b/><sz val="12"/><color rgb="FF0F172A"/><name val="Calibri"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF0284C7"/><bgColor indexed="64"/></patternFill></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="3">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>
  </cellXfs>
</styleSheet>');

            // 6. xl/worksheets/sheet1.xml
            $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>';

            $rowIdx = 1;

            // Title row
            if ($title) {
                $sheetXml .= '<row r="' . $rowIdx . '"><c r="A' . $rowIdx . '" t="inlineStr"><is><t>' . htmlspecialchars($title) . '</t></is></c></row>';
                $rowIdx++;
                $sheetXml .= '<row r="' . $rowIdx . '"><c r="A' . $rowIdx . '" t="inlineStr"><is><t>PT IRC INOAC INDONESIA - Tanggal: ' . date('d-m-Y H:i:s') . '</t></is></c></row>';
                $rowIdx += 2;
            }

            foreach ($sections as $section) {
                if (!empty($section['section_title'])) {
                    $sheetXml .= '<row r="' . $rowIdx . '"><c r="A' . $rowIdx . '" t="inlineStr" s="2"><is><t>' . htmlspecialchars($section['section_title']) . '</t></is></c></row>';
                    $rowIdx++;
                }

                if (!empty($section['headers'])) {
                    $sheetXml .= '<row r="' . $rowIdx . '">';
                    $colIdx = 0;
                    foreach ($section['headers'] as $h) {
                        $cellRef = self::getColName($colIdx) . $rowIdx;
                        $sheetXml .= '<c r="' . $cellRef . '" t="inlineStr" s="1"><is><t>' . htmlspecialchars((string) $h) . '</t></is></c>';
                        $colIdx++;
                    }
                    $sheetXml .= '</row>';
                    $rowIdx++;
                }

                if (!empty($section['rows'])) {
                    foreach ($section['rows'] as $row) {
                        $sheetXml .= '<row r="' . $rowIdx . '">';
                        $colIdx = 0;
                        foreach ($row as $val) {
                            $cellRef = self::getColName($colIdx) . $rowIdx;
                            $valStr = (string) $val;
                            if (is_numeric($val) && !str_starts_with($valStr, '0')) {
                                $sheetXml .= '<c r="' . $cellRef . '"><v>' . $val . '</v></c>';
                            } else {
                                $sheetXml .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . htmlspecialchars($valStr) . '</t></is></c>';
                            }
                            $colIdx++;
                        }
                        $sheetXml .= '</row>';
                        $rowIdx++;
                    }
                }

                $rowIdx += 2; // 2 blank lines gap between sections
            }

            $sheetXml .= '  </sheetData>
</worksheet>';

            $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
            $zip->close();
        }

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0'
        ])->deleteFileAfterSend(true);
    }

    private static function getColName($index)
    {
        $letters = '';
        while ($index >= 0) {
            $letters = chr($index % 26 + 65) . $letters;
            $index = intval($index / 26) - 1;
        }
        return $letters;
    }
}
