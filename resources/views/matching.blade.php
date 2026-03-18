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
            if (res.ok)
                showOutput(json);
            else alert('Something went wrong');
        }

        function showOutput(json) {
            if (json.error) alert(json.error);
            const matchHolder = document.getElementById('match');
            matchHolder.innerHTML="";
            if (json.match) {
                for (let match of json.match) {
                    matchHolder.appendChild(outputHTML(match));
                }
                matchHolder.style.display = 'initial';
            } else {

                matchHolder.innerHTML = "<b>No match</b>";            
                matchHolder.style.display = 'initial';
            }
        }

        function outputHTML(match) {
            const matchContainer = document.createElement('div');
            const matchcount = document.createElement('input');
            matchcount.type = 'range';
            matchcount.value = match.amount;
            matchcount.min = 0;
            matchcount.disabled=true;
            const matchInfo = document.createElement('p');
            matchInfo.innerHTML = `
        Name: ${match.user.display_name}<br/>
        Interest Tag: ${match.user.interest_tag.split(',').sort().join(',')}<br/>
        Match Percentage: ${match.amount}%<br/>
        Not a match on: ${match.notMatchingHobbies.split(',').sort().join(',')}
    `;

            matchContainer.appendChild(matchInfo); // Add the paragraph to the container
            matchContainer.appendChild(matchcount); // Add the input to the container

            return matchContainer; matchcount.max = 100;
        }
    </script>
</body>

</html>