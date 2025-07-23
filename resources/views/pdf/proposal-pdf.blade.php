<!DOCTYPE html>
<html>
<head>
    <title>Proposal details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #2D4547;
            margin-bottom: 10px;
            text-align: center;
        }

        p {
            margin-bottom: 10px;
        }

        .table-wrapper {
            page-break-inside: avoid;
            width: 100%;
        }

        .table, .nested-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table tr, .nested-table tr {
            page-break-inside: auto;
        }

        .nested-table tr {
            page-break-inside: auto;
        }

        .table td, .nested-table td {
            border: 1px solid #ddd;
            page-break-inside: auto;
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
            color: #333;
        }

        .table .key-column {
            width: 30%;
        }

        .table .value-column {
            width: 70%;
        }
    </style>
</head>
<body>
<table class="table">
    <th colspan="2" style="text-align: center">Proposal details</th>
    <tbody>
    @foreach($proposalInfo['proposalDetails'] as $key => $attribute)
        @if($key == 'attachments')
            @php
                $i = 1;
            @endphp
            @foreach($attribute as $caption => $fileName)
                <tr>
                    <td class="key-column"><strong>Attachment {{ $i++ }}</strong></td>
                    @if(str_contains($fileName,env('APP_URL')))
                        <td>
                            <a target="_blank" href="{{ $fileName }}"> Download file</a>
                            <p> {{ $caption }}</p>
                        </td>
                @endif
                <tr>
            @endforeach
        @else
            <tr>
                <td class="key-column"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                @if (str_contains($attribute,env('APP_URL')))
                    <td>
                        <a target="_blank" href="{{ $attribute }}"> Download file</a>
                    </td>
                @else
                    <td class="value-column">{{ $attribute }}</td>
                @endif
            </tr>
        @endif
    @endforeach
    </tbody>
</table>

<table class="table">
    <th colspan="2" style="text-align: center">Member details</th>
    <tbody>
    @foreach($proposalInfo['usersDetails'] as $key => $attribute)
        <tr>
            <td class="key-column"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
            <td class="value-column">
                @if($key == 'partners')
                    {!! nl2br(e($attribute)) !!}
                @else
                    {{ $attribute }}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(isset($proposalInfo['archlab_section']))
    <table class="table">
        <th colspan="2" style="text-align: center">Archlab</th>
        <tbody>
        @foreach($proposalInfo['archlab_section'] as $key => $attribute)
            <tr>
                <td class="key-column"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                <td class="value-column">{{ $attribute }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

@if(isset($proposalInfo['fixlab_section']))
    <div class="table-wrapper">
        <table class="table">
            <th colspan="2" style="text-align: center">Fixlab</th>
            <tbody>
            <!-- Prima visualizziamo gli attributi non array -->
            @foreach($proposalInfo['fixlab_section'] as $key => $attribute)
                @if(!is_array($attribute))
                    <tr>
                        <td class="key-column"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                        <td class="value-column">{{ $attribute }}</td>
                    </tr>
                @endif
            @endforeach
            @foreach($proposalInfo['fixlab_section'] as $key => $attribute)
                @if(is_array($attribute))
                    <tr>
                        <td colspan="2"> <!-- Rimuove la key-column e span su 2 colonne -->
                            @php
                                $i = 1;
                            @endphp
                            @foreach($attribute as $object)
                                <table class="nested-table">
                                    <tbody>
                                    <tr>
                                        <th colspan="2">
                                            <strong>Object: {{ $i++}}</strong>
                                        </th>
                                    </tr>
                                    @foreach($object as $keyDetails => $details)
                                        <tr>
                                            <td class="key-column">
                                                <strong>{{ ucfirst(str_replace('_', ' ', str_replace('fixlab_', '', $keyDetails))) }}</strong>
                                            </td>
                                            <td class="value-column">
                                                @if(is_array($details))
                                                    {{ implode(', ', $details) }}
                                                @else
                                                    {{ $details }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endif


@if(isset($proposalInfo['molab_section']))
    <div class="table-wrapper">
        <table class="table">
            <th colspan="2" style="text-align: center">Molab</th>
            <tbody>
            <!-- Prima visualizziamo gli attributi non array -->
            @foreach($proposalInfo['molab_section'] as $key => $attribute)

                @if(!is_array($attribute))
                    <tr>
                        <td class="key-column"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                        @if (str_contains($attribute,env('APP_URL')))
                            <td>
                                <a target="_blank" href="{{ $attribute }}"> Download file</a>
                            </td>
                        @else
                            <td class="value-column">{{ $attribute }}</td>
                        @endif

                    </tr>
                @endif
            @endforeach
            @foreach($proposalInfo['molab_section'] as $key => $attribute)
                @if(is_array($attribute))
                    <tr>
                        <td colspan="2"> <!-- Rimuove la key-column e span su 2 colonne -->
                            @php
                                $i = 1;
                            @endphp
                            @foreach($attribute as $object)
                                <table class="nested-table">
                                    <tbody>
                                    <tr>
                                        <th colspan="2">
                                            <strong>Object: {{ $i++}}</strong>
                                        </th>
                                    </tr>
                                    @foreach($object as $keyDetails => $details)
                                        <tr>
                                            <td class="key-column">
                                                <strong>{{ ucfirst(str_replace('_', ' ', str_replace('fixlab_', '', $keyDetails))) }}</strong>
                                            </td>
                                            <td class="value-column">
                                                @if(is_array($details))
                                                    {{ implode(', ', $details) }}
                                                @else
                                                    @if (str_contains($details,env('APP_URL')))
                                                        <a target="_blank" href="{{ $details }}"> Download file</a>
                                                    @else
                                                        {{ $details }}
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(isset($proposalInfo['servicesDetails']))
    <table class="table">
        <th colspan="2" style="text-align: center">Services details</th>
        @php $i = 1; @endphp
        @foreach($proposalInfo['servicesDetails'] as $service)
            <tr>
                <td colspan="2">
                    <table class="nested-table">
                        <thead>
                            <tr>
                                <th colspan="2" style="text-align: center">Service {{ $i++ }}</th>
                            </tr>
                            </thead>
                        @foreach($service as $key => $attribute)
                            @if($key!='method_and_tool')
                                <tr>
                                    <td class="key-column"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                    </td>
                                    <td class="value-column">{{ $attribute }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <thead>
                        <tr>
                            <th style="text-align: center;" class="key-column">Method</th>
                            <th style="text-align: center;">Tool</th>
                        </tr>
                        </thead>
                        @foreach($service['method_and_tool'] as $item)
                            <tr>
                                <td class=key-column">{{ $item['method'] }}</td>
                                <td class="value-column">{{ $item['tool'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        @endforeach
    </table>
@endif
