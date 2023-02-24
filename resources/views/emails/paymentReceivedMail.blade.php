<style>
    .wrapper {
        font-family: sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        max-width: 350px;
        margin:0 auto;
        min-height:100vh;
    }
    .btn {
        padding: 10px 20px;
        font-size: 20px;
        background-color: dodgerblue;
        color: whitesmoke;
        text-decoration: none;
        border-radius: 20px;
        margin-top: 25px;
    }
    .name {
        text-transform: capitalize;
    }
</style>
<div class="wrapper">
    <p><span class="name">{{$payerName}}</span> betaalde <span class="name">{{$receiverName}}</span> €{{$balance}} uit.</p>
</div>




