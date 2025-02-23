<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transactions Report</title>
    <style media="all">
        *,
        ::after,
        ::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            color: #333;
            font-family: 'bengali', sans-serif !important;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.5em;
        }

        main{
            padding: 50px 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid white;
            border-collapse: collapse;
        }

        table > th{
            font-weight: bold;
        }

        th, td {
            border: 1px solid #fde1e1;
            padding: 6px 12px;
        }

        b{
            border-bottom: 1px dashed black;
        }


    </style>
</head>

<body>

<main>
    <div style="width: 100%; text-align: center; margin-bottom: 30px;">
        <h3 style="margin: 0; ">{{$company['0']->name ?? 'N/A'}}</h3>
        <span>Phone: {{$company['0']->phone ?? 'N/A'}}</span>
        <address>Address: {{$company['0']->address ?? 'N/A'}}</address>
    </div>

    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>A/C No</th>
            <th>Date</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Profit</th>
        </tr>
        </thead>

        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{$transaction->account->name}}</td>
                <td>{{$transaction->account->number}}</td>
                <td>{{formatDate($transaction->created_at)}}</td>
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
            </tr>
        @endforeach
        </tbody>
    </table>

</main>

</body>

</html>
