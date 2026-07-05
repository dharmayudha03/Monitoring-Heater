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
            <Font ss:FontName="Calibri" ss:Size="14" ss:Color="#0F172A" ss:Bold="1"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="SubTitle">
            <Font ss:FontName="Calibri" ss:Size="12" ss:Color="#0284C7" ss:Bold="1"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
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
        <Style ss:ID="BadgeWarning">
            <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#854D0E" ss:Bold="1"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="BadgeDanger">
            <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#991B1B" ss:Bold="1"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
    </Styles>
    <Worksheet ss:Name="Histori Logs">
        <Table>
            <Column ss:Width="40" />
            <Column ss:Width="140" />
            <Column ss:Width="100" />
            <Column ss:Width="140" />
            <Column ss:Width="130" />
            <Column ss:Width="100" />
            <Column ss:Width="100" />

            <Row ss:Height="25">
                <Cell ss:StyleID="Title"><Data ss:Type="String">PT IRC INOAC INDONESIA</Data></Cell>
            </Row>
            <Row ss:Height="20">
                <Cell ss:StyleID="SubTitle"><Data ss:Type="String">LAPORAN HISTORI MONITORING HEATER</Data></Cell>
            </Row>
            <Row ss:Height="18">
                <Cell ss:StyleID="DataCenter"><Data ss:Type="String">Tanggal Ekspor: {{ date('d-m-Y H:i:s') }} WIB |
                        Total Record: {{ $logs->count() }} Data</Data></Cell>
            </Row>
            <Row ss:Height="15"></Row>

            <Row ss:Height="22">
                <Cell ss:StyleID="Header"><Data ss:Type="String">No</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Waktu Log</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Kode Heater</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Heater</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Zona</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Current (A)</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Status</Data></Cell>
            </Row>

            @foreach ($logs as $idx => $log)
                @php
                    $status = $log->status;
                    $style = 'DataCenter';
                    if ($status === 'NORMAL') {
                        $style = 'BadgeNormal';
                    }
                    if ($status === 'WARNING') {
                        $style = 'BadgeWarning';
                    }
                    if ($status === 'DANGER') {
                        $style = 'BadgeDanger';
                    }
                @endphp
                <Row ss:Height="20">
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ $idx + 1 }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data
                            ss:Type="String">{{ $log->received_at ? $log->received_at->format('d-m-Y H:i:s') : '-' }}</Data>
                    </Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $log->heater->heater_code ?? '-' }}</Data>
                    </Cell>
                    <Cell ss:StyleID="DataLeft"><Data ss:Type="String">{{ $log->heater->heater_name ?? '-' }}</Data>
                    </Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">{{ $log->heater->zone ?? '-' }}</Data></Cell>
                    <Cell ss:StyleID="DataCenter"><Data ss:Type="Number">{{ number_format($log->current, 2) }}</Data>
                    </Cell>
                    <Cell ss:StyleID="{{ $style }}"><Data ss:Type="String">{{ $status }}</Data></Cell>
                </Row>
            @endforeach
        </Table>
    </Worksheet>
</Workbook>
