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

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table tr {
            page-break-inside: auto;
        }

        .table td {
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
    <th colspan="2" style="text-align: center"> {{ $details['proposal'] }}</th>
    <tbody>
    @foreach($details['reportDetails'] as $key => $attribute)
        @if($key == 'files')
            <tr>
                <td class="key-column"><strong>Files</strong></td>
                <td>
                    @php
                        $i = 1;
                    @endphp
                    @foreach($attribute as $attachment)
                        <a target="_blank" href="{{ $attachment }}">File {{$i++}}</a>
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                </td>
            </tr>
        @elseif( $key == 'photos')
            <tr>
                <td class="key-column"><strong>Photos</strong></td>
                <td>
                    @php $j = 1; @endphp
                    @foreach($attribute as $attachment)
                        <a target="_blank" href="{{ $attachment }}">Photo {{$j++}}</a>
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                </td>
            </tr>
        @else
            <tr>
                <td class="key-column"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                <td class="value-column">{{ $attribute }}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
</body>
