<style>
    .wrapper {
        font-family: sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: left;
        align-items: center;
        max-width: 600px;
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

    .bold {
        font-weight: bold;
    }

    .link {
        color: dodgerblue;
        text-decoration: none;
    }
</style>
<div class="wrapper">
    <h4>Hi <span class="name">{{$invitee->name}}</span>,</h4>

    <p>
        Great news! You’ve been invited to join {{ $company->name }} on Sendwich, the convenient way to organize team lunches.
        Say goodbye to the hassle of coordinating everyone's orders and hello to a streamlined process that makes ordering lunch simple and fun.
    </p>
    <p>
        <span class="bold">Here’s how to get started:</span>
    </p>

    <ol>
        <li>
            Accept the Invitation: Click on this link to set up your account. It's quick and easy!
            <a class="link" href="{{$signedUrl}}">Accept Invitation</a>
            <br />
        </li>

        <li>
            Explore Your Options: Once you’re in, you can explore the stores and menus that your team has already added. Feel the freedom to suggest new favorite spots too!
        </li>

        <li>
            Order with Ease: When you're ready, select your meal with just a click and let Sendwich handle the coordination. We'll make sure you never miss out on a delicious lunch!
        </li>
    </ol>

    <p>
        By joining Sendwich, you’re not only gaining access to a platform that simplifies meal ordering but also becoming a part of a community that enjoys food together. We can’t wait to see what you’ll order first.
    </p>

    <p>
        Welcome aboard and happy ordering!
    </p>

    <a class="btn" href="{{$signedUrl}}">Accept Invitation</a>
</div>




