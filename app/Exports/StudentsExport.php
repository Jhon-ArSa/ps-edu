<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class StudentsExport
{
    /**
     * @param Collection<int, User> $users
     */
    public function __construct(private readonly Collection $users)
    {
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Rol',
            'Codigo',
            'Correo',
            'DNI',
            'Telefono',
            'Programa',
            'Promocion',
            'Estado',
            'Fecha de registro',
        ];
    }

    public function map(User $user): array
    {
        return [
            $user->name,
            ucfirst($user->role),
            $user->alumnoProfile?->code ?? '-',
            $user->email,
            $user->dni ?? '-',
            $user->phone ?? '-',
            $user->alumnoProfile?->program ?? '-',
            $user->alumnoProfile?->promotion_year ?? '-',
            $user->status ? 'Activo' : 'Inactivo',
            $user->created_at?->format('d/m/Y H:i') ?? '-',
        ];
    }

    public function downloadCsv(string $filename): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $this->headings());

            foreach ($this->users as $user) {
                fputcsv($handle, $this->map($user));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadXlsx(string $filename)
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'users_export_');
        $zip = new ZipArchive();

        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'No se pudo generar el archivo XLSX.');
        }

        $sheetRowsXml = $this->buildSheetRowsXml();

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($sheetRowsXml));

        $zip->close();

        $binary = file_get_contents($tmpPath);
        @unlink($tmpPath);

        return response($binary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function buildSheetRowsXml(): string
    {
        $rowsXml = $this->rowXml(1, $this->headings());
        $rowNumber = 2;

        foreach ($this->users as $user) {
            $rowsXml .= $this->rowXml($rowNumber, $this->map($user));
            $rowNumber++;
        }

        return $rowsXml;
    }

    /**
     * @param array<int, mixed> $values
     */
    private function rowXml(int $rowNumber, array $values): string
    {
        $xml = '<row r="' . $rowNumber . '">';

        foreach ($values as $index => $value) {
            $col = $this->columnLetter($index + 1);
            $ref = $col . $rowNumber;
            $text = $this->xmlEscape((string) $value);
            $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . $text . '</t></is></c>';
        }

        $xml .= '</row>';

        return $xml;
    }

    private function columnLetter(int $index): string
    {
        $result = '';
        while ($index > 0) {
            $index--;
            $result = chr(65 + ($index % 26)) . $result;
            $index = intdiv($index, 26);
        }

        return $result;
    }

    private function xmlEscape(string $value): string
    {
        $value = preg_replace('/[^\x09\x0A\x0D\x20-\x{FFFD}]/u', '', $value) ?? '';
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheets><sheet name="Usuarios" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }

    private function sheetXml(string $rowsXml): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . '</worksheet>';
    }
}
