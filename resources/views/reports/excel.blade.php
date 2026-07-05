<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:html="http://www.w3.org/TR/REC-html40">
    <Styles>
        <Style ss:ID="Header">
            <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/><Interior ss:Color="#0284C7" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="Title">
            <Font ss:FontName="Calibri" ss:Size="14" ss:Color="#0F172A" ss:Bold="1"/><Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="Section">
            <Font ss:FontName="Calibri" ss:Size="12" ss:Color="#0F172A" ss:Bold="1"/><Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="DataCenter">
            <Font ss:FontName="Calibri" ss:Size="11"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="DataLeft">
            <Font ss:FontName="Calibri" ss:Size="11"/><Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="BadgeNormal">
            <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#166534" ss:Bold="1"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="BadgeDanger">
            <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#991B1B" ss:Bold="1"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
    </Styles>
    <Worksheet ss:Name="Laporan Performansi">
        <Table>
            <Column ss:Width="40" />
            <Column ss:Width="100" />
            <Column ss:Width="140" />
            <Column ss:Width="120" />
            <Column ss:Width="120" />
            <Column ss:Width="120" />
            <Column ss:Width="130" />
            <Column ss:Width="130" />

            <Row ss:Height="25">
                <Cell ss:StyleID="Title"><Data ss:Type="String">LAPORAN EXECUTIVE PERFORMA &amp; KEANDALAN HEATER</Data>
                </Cell>
            </Row>
            <Row ss:Height="18">
                <Cell ss:StyleID="DataLeft"><Data ss:Type="String">PT IRC INOAC Indonesia | Tanggal Ekspor:
                        {{ date('d-m-Y H:i:s') }}</Data></Cell>
            </Row>
            <Row ss:Height="15"></Row>

            <Row ss:Height="22">
                <Cell ss:StyleID="Section"><Data ss:Type="String">1. RINGKASAN REKAPITULASI HEATER</Data></Cell>
            </Row>
            <Row ss:Height="22">
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Unit</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Record Logs</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Replacement</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Log Normal</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Log Warning</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Log Danger</Data></Cell>
            </Row>
            <Row ss:Height="20">
                <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $totalHeaters }}</Data></Cell>
                <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $totalLogs }}</Data></Cell>
                <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $totalReplacements }}</Data></Cell>
                <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $normalLogsCount }}</Data></Cell>
                <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $warningLogsCount }}</Data></Cell>
                <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $dangerLogsCount }}</Data></Cell>
            </Row>
            <Row ss:Height="15"></Row>

            <Row ss:Height="22">
                <Cell ss:StyleID="Section"><Data ss:Type="String">2. PERFORMA &amp; KESEHATAN SELURUH HEATER</Data>
                </Cell>
            </Row>
            <Row ss:Height="22">
                <Cell ss:StyleID="Header"><Data ss:Type="String">No</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Kode Heater</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Heater</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Zona</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Arus Terakhir (A)</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Status Kesehatan</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Replacement</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Danger Alert</Data></Cell>
            </Row>
            @foreach ($heatersSummary as $index => $hs)
                @php
                    $log = $hs->latestLog;
                    $status = $log ? $log->status : 'OFFLINE';
                    $style = 'DataCenter';
                    if ($status === 'NORMAL') {
                        $style = 'BadgeNormal';
                    }
                    if ($status === 'DANGER') {
                        $style = 'BadgeDanger';
                    }
                @endphp
                <Row ss:Height="20">
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $index + 1 }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $hs->heater_code }}</Data></Cell>
                    <Cell ss:StyleID="DataLeft"><Data ss:Type="String">{{ $hs->heater_name }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $hs->zone }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data
                            ss:Type="String">{{ $log ? number_format($log->current, 2) : '-' }}</Data></Cell>
                    <Cell ss:StyleID="{{ $style }}"><Data ss:Type="String">{{ $status }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $hs->total_replacements }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $hs->danger_alerts_count }}</Data></Cell>
                </Row>
            @endforeach
            <Row ss:Height="15"></Row>

            <Row ss:Height="22">
                <Cell ss:StyleID="Section"><Data ss:Type="String">3. RIWAYAT PENGGANTIAN HEATER TERAKHIR (REPLACEMENT
                        LOGS)</Data></Cell>
            </Row>
            <Row ss:Height="22">
                <Cell ss:StyleID="Header"><Data ss:Type="String">No</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Waktu Penggantian</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Kode Heater</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Kode Lama</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Kode Baru</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Teknisi / Petugas</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Alasan Penggantian</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Catatan</Data></Cell>
            </Row>
            @foreach ($recentReplacements as $idx => $rep)
                <Row ss:Height="20">
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $idx + 1 }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data
                            ss:Type="String">{{ $rep->replacement_date ? $rep->replacement_date->format('d-m-Y H:i') : '-' }}</Data>
                    </Cell>
                    <Cell ss:StyleID="DataCenter"><Data
                            ss:Type="String">{{ $rep->heater->heater_code ?? '-' }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $rep->old_heater_code }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $rep->new_heater_code }}</Data></Cell>
                    <Cell ss:StyleID="DataLeft"><Data ss:Type="String">{{ $rep->replaced_by }}</Data></Cell>
                    <Cell ss:StyleID="DataLeft"><Data ss:Type="String">{{ $rep->reason }}</Data></Cell>
                    <Cell ss:StyleID="DataLeft"><Data ss:Type="String">{{ $rep->notes }}</Data></Cell>
                </Row>
            @endforeach
        </Table>
    </Worksheet>
</Workbook>
