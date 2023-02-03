<div>
    <p>Beste {{$order->user->name}},</p>
    <p>{{$order->deliverer->name}} paste je bestelling aan van {{\Carbon\Carbon::parse($order->date)->format('d/m/Y')}}</p>
    <table>
        <tr>
            <td><b>Originele bestelling</b></td>
            <td>{{$oldProductName}}</td>
        </tr>
        <tr>
            <td><b>Aangepaste bestelling</b></td>
            <td{{$newProductName}}></td>
        </tr>
    </table>
</div>
