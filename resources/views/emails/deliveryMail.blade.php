<div>
    <p>Beste {{$user->name}},</p>
    <p>Jij bent de uitverkorene om vandaag broodjes op te halen.</p>
    <table>
        @foreach($orders as $order)
            <tr>
                <td>
                    <p>{{ $order->user->name }}</p>
                </td>
                <td>
                    <p>{{ $order->product->name }} ({{$order->comment}})</p>
                </td>
                <td>
                    <p>{{ $order->product->price }}</p>
                </td>
            </tr>
        @endforeach
    </table>
</div>
