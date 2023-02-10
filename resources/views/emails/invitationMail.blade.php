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
    <h4>Hallo <span class="name">{{$invitee->name}}</span>,</h4>
    <p><span class="name">{{ $company->name }}</span> nodigt je uit voor de sendwich dienst .</p>
    <p>Bevestig hier je deelname </p>
    <a class="btn" href="{{$signedUrl}}">Ja ik doe mee !</a>
</div>




