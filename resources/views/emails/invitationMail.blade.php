<style>
    .wrapper {
        font-family: sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 500px;
    }
    .btn {
        padding: 10px 20px;
        font-size: 20px;
        background-color: dodgerblue;
        color: whitesmoke;
        text-decoration: none;
        border-radius: 20px;
    }
    .name {
        text-transform: capitalize;
    }
</style>
<div class="wrapper">
    <h4>Hallo <span class="name">{{$invitee->name}}</span>{{$invitee->name}},</h4>
    <p>Je bent uitgenodigd voor de sendwich dienst .</p>
    <p>Bevestig je deelname </p>
    <a class="btn" href="{{$signedUrl}}">Ja ik doe mee !</a>
</div>




