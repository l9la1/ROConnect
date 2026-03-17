<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>test</title>
</head>

<body>

    <input type="text" id="hobby" placeholder="seperated by , hobbys" />
    <input type="number" id="minHob" min="0" value="0" placeholder="The amount of hobbys that has to match" />
    <input type="submit" value="search" onclick="submit()" />
    
    <div style="display:none;" id="match">
        <input type="range" min="0" max="100" id="matchcount" disabled />
        <div id="out"></div>
    </div>
    <script>
        async function submit() {
            const hobby = document.getElementById('hobby');
            const minHob = document.getElementById('minHob');
            if (parseInt(minHob.value) > hobby.value.split(',').length) {
                alert('The minHob has to be lower or equal to the given hobbys');
                return;
            }

            const res = await fetch('http://127.0.0.1:8000/matching?hobbies=' + JSON.stringify(
                hobby.value.split(',').map(dt => dt.trim().toLowerCase())
            ) + "&minHob=" + minHob.value);

            const json = await res.json();
            if(res.ok)
            showOutput(json);
            else alert('Something went wrong');
        }
        
        function showOutput(json){
            if(json.error)alert(json.error);
            if(json.match)
        {
            const match=document.getElementById('match');
            const matchcount=document.getElementById('matchcount');
            const out=document.getElementById('out');
            
            matchcount.value=json.amount;
            out.innerHTML=`
            Name:${json.match.display_name}<br/>
            interest_tag:${json.match.interest_tag}<br/>
            matchpercentage:${json.amount}%<br/>
            not a match on:${json.notMatchingHobbies}
            `;
            match.style.display='initial';

        }
        }
    </script>
</body>

</html>