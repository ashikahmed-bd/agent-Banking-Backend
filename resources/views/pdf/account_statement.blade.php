<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Customers Report</title>
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
    <table style="margin-bottom: 30px;">
        <thead>
        <tr>
            <th style="text-align: left; border: none;">
                <h3>Company: {{$company->name}}</h3>
                <span>Phone: {{$company->phone}}</span>
                <address>Address: {{$company->address}}</address>
            </th>
            <th style="text-align: right; border: none;">
                <span>Period From: {{$date}}</span>
                <h3>Account Name: {{$account->name}}</h3>
                @if($account->number == null)
                    <address>Account No: Default</address>
                @else
                    <address>Account No: {{$account->number}}</address>
                @endif
            </th>
        </tr>
        </thead>
    </table>


    <table style="margin-bottom: 30px;">
        <thead>
        <tr>
            <th>Date</th>
            <th>Sender</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Fee</th>
            <th>Reference</th>
            <th>Remark</th>
        </tr>
        </thead>

        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{formatDate($transaction->created_at)}}</td>
                <td>{{$transaction->sender->name}}</td>
                <td>{{$transaction->type}}</td>
                <td>{{$transaction->amount}}</td>
                <td>{{$transaction->fee}}</td>
                <td>{{$transaction->reference}}</td>
                <td>{{$transaction->remark}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table style="">
        <thead>
        <tr>
            <th style="text-align: left; border: none;">
                <p>Total Deposit: {{number_format($deposit)}}</p>
                <p>Total Withdrawal: {{number_format($withdraw)}}</p>
            </th>

            <th style="text-align: right; border: none;">
                <p>Opening Balance: {{number_format($account->opening_balance)}}</p>
                <p>Current Balance: {{number_format($account->current_balance)}}</p>
            </th>
        </tr>
        </thead>
    </table>

    <p style="text-align: center;">--------------------------------------  End of Statement  --------------------------------------</p>

</main>
</body>

</html>
