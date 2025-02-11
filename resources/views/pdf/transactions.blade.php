<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>HTML table border Attribute</title>
    <style>
        table {
            margin: 0 auto;
            width: 100%;
            text-align: left;
        }
        table, th, td {
            border: 1px solid #0a082a;
            border-collapse: collapse;
        }

        th{
            padding: 0 6px;
        }

        td{
            padding: 0 6px;

        }
    </style>
</head>

<body>
<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>A/C No</th>
        <th>Credit</th>
        <th>Debit</th>
        <th>Commission</th>
        <th>Net Amount</th>
    </tr>
    </thead>

    <tbody>
    @foreach($transactions as $transaction)
        <tr>
            <td>{{$transaction->account->name}}</td>
            <td>{{$transaction->account->number}}</td>
            @if($transaction->type === 'credit')
                <td style="text-align: right;">{{$transaction->amount}}</td>
            @else
                <td></td>
            @endif

            @if($transaction->type === 'debit')
                <td style="text-align: right;">{{$transaction->amount}}</td>
            @else
                <td></td>
            @endif

            <td style="text-align: right;">{{$transaction->commission}}</td>
            <td style="text-align: right;">{{$transaction->net_amount}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>

</html>
