
<template>
    <div class="box">
        <table class="table">
            <thead>
                <th>Player Name</th>
                <th>Plays</th>
                <th>Wins</th>
                <th>Win Rate</th>
                <th>Expected Win Rate</th>
                <th>Adjusted Rate</th>
                <th>Expected Wins</th>
                <th>WOE</th>
            </thead>
            <tr v-for="player in players">
                <td>{{player.name}}</td>
                <td>{{player.play_count}}</td>
                <td>{{player.wins}}</td>
                <td>{{player.win_rate | round }}%</td>
                <td>{{player.expected_win_rate | round}}%</td>
                <td>{{player.adjusted_win_rate | round}}%</td>
                <td>{{player.expected_wins}}</td>
                <td>{{player.woe}}</td>


            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {players: []}
        },
        filters: {
            round: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value * 100 * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor ;
            }
        },

        created() {
            axios.get('/players/json').then(response => this.players = response.data)
            console.log(this.players);
        }

    }
</script>