<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>HTML table border Attribute</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        main{
            padding: 30px 50px;
        }
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
            padding: 4px 8px;
        }

        td{
            padding: 4px 8px;
        }

    </style>
</head>

<body>

<main>
    <div style="width: 100%; text-align: center;">
        <h3 style="margin: 0; ">{{$company['0']->name ?? 'N/A'}}</h3>
        <span>Phone: {{$company['0']->phone ?? 'N/A'}}</span>
        <address>Address: {{$company['0']->address ?? 'N/A'}}</address>
    </div>

    <table style="padding: 20px 0; border: none; text-align: center;">
        <tbody>
        <tr>
            <td style="border: none;">Cash: </td>
            <td style="border: none;">Accounts: </td>
            <td style="border: none;">Total Due:{{$total_due}}</td>
            <td style="border: none;">Total Payable: {{$total_payable}}</td>
        </tr>
        </tbody>
    </table>

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
