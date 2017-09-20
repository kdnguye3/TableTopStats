
<template>
    <div class="box">
        <div class="field">
            <label class="label">Group</label>
            <div class="select" >
                <select v-on:change="submit()" v-model="group_value">
                    <option :value="0">
                        All Players
                    </option>
                    <option :value="group.id"  v-for="group in groups">
                        {{group.name}}
                    </option>
                </select>
            </div>
            <label class="label">Season</label>
            <div class="select">
                <select v-on:change="submit()" v-model="season">
                    <option value="0">All years</option>
                    <option value="1">Season 1</option>
                    <option value="2">Season 2</option>
                </select>
            </div>

        </div>
        <table class="table">
            <thead>
                <th>Placement</th>
                <th>Player Name</th>
                <th>Plays</th>
                <th>Wins</th>
                <th>Win Rate</th>
                <th>Expected Win Rate</th>
                <th>Adjusted Rate</th>
                <th>Expected Wins</th>
                <th>WOE</th>
            </thead>
            <tr v-for="(player,index) in players">
                <th>{{index + 1}}</th>
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
            return {players: [], groups: [], group_value: 0, season: 0}
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
            axios.post('/players/json',{
                'group' : this.group_value,
                'season' : this.season
            }).then(response => {
                    this.players = response.data.players;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                }
            );
            axios.post('https://www.boardgamegeek.com/xmlapi2/thing?id=145639&type=boardgame').then(response => {
                console.log("bgg");
                console.log(response);
                }
            );
        },

        methods: {
            submit() {
                console.log("posted", this.season, this.group_value);
                axios.post('/players/json',{
                    'group' : this.group_value,
                    'season' : this.season
                }).then(response => {
                    console.log("receive", response.data);

                    this.players = response.data.players;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                    }
                );
            }
        }


    }
</script>