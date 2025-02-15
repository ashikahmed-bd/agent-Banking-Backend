<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Customers Report</title>
    <style media="all">
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
