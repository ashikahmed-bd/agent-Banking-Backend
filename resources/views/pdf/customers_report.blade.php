<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Customers Report</title>
    <style media="all">
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

        }

        td{

        }

    </style>
</head>

<body>


<table style="margin-bottom: 20px; border: none;">
    <thead style="border: none;">
    <tr style="border: none;">
        <th style="border: none; text-align: left;">{{$company['0']->name ?? 'N/A'}}</th>
        <th style="border: none; text-align: right;">Report Time: {{$date}}</th>
    </tr>
    </thead>
</table>

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
            <td>{{$customer->payments()->where('type', '=', 'credit')->sum('amount')}}</td>
            <td>{{$customer->payments()->where('type', '=', 'debit')->sum('amount')}}</td>
            <td>{{$customer->balance}}</td>
        </tr>
    @endforeach
    </tbody>

    <tfoot>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th>Total Due:{{$total_due}}</th>
        <th>Total Payable: {{$total_payable}}</th>
    </tr>
    </tfoot>
</table>
</body>

</html>
