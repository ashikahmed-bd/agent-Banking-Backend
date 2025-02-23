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

    <div style="width: 100%; text-align: center;">
        <h3 style="margin: 0; ">{{$company['0']->name ?? 'N/A'}}</h3>
        <span>Phone: {{$company['0']->phone ?? 'N/A'}}</span>
        <address>Address: {{$company['0']->address ?? 'N/A'}}</address>
    </div>

    <div style="width: 100%; text-align: center; padding: 20px 0;">
        <span style="font-weight: bold; color: #f12929; padding: 0 10px;">Total Due:{{$total_due}}</span>
        <span style="font-weight: bold; color: #14a252; padding: 0 10px;">Total Payable: {{$total_payable}}</span>
    </div>

    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Balance</th>
        </tr>
        </thead>

        <tbody>
        @foreach($customers as $customer)
            <tr>
                <td>{{$customer->name}}</td>
                <td>{{$customer->phone}}</td>
                <td style="text-align: right;">{{$customer->payments()->where('type', '=', 'credit')->sum('amount')}}</td>
                <td style="text-align: right;">{{$customer->payments()->where('type', '=', 'debit')->sum('amount')}}</td>
                <td style="text-align: right;">{{$customer->balance}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</main>
</body>

</html>
