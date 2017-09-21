
<template>
    <div class="box">
        <div class="field">
            <label class="label">Group</label>
            <div class="control">
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
            </div>
        </div>
        <div class="field">
            <label class="label">Season</label>
            <div class="control">
                <div class="select">
                    <select v-on:change="submit()" v-model="season">
                        <option value="0">All years</option>
                        <option value="1">Season 0</option>
                        <option value="2">Season 1</option>
                    </select>
                </div>
            </div>
        </div>
        <table class="table">
            <thead>
                <th>Game</th>
                <th>Plays</th>
                <th>Wins</th>
                <th>Win Rate</th>
                <th>Adjusted Plays</th>
                <th>Adjusted Wins</th>
            </thead>
            <tr v-for="game in plays">
                <td>{{game.name}}</td>
                <td>{{game.play_count}}</td>
                <td>{{game.wins}}</td>
                <td>{{game.win_rate | percent }}%</td>
                <td>{{game.new_play_count | round}}</td>
                <td>{{game.new_wins | round}}</td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        props:['id'],
        data() {
            return {plays: [], groups: [], group_value: 0, season: 0}
        },
        filters: {
            percent: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value * 100 * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor ;
            },
            round: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value  * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor ;
            }
        },

        created() {
            console.log("yo",this.id);

            axios.post('/players/' + this.id + '/json',{
                'group' : this.group_value,
                'season' : this.season
            }).then(response => {
                    this.plays = response.data.plays;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                }
            );

        },

        methods: {
            submit() {
                console.log("posted", this.season, this.group_value);
                axios.post('/players/'+ this.id +'/json',{
                    'group' : this.group_value,
                    'season' : this.season
                }).then(response => {
                        console.log("receive", response.data);

                    this.plays = response.data.plays;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                    }
                );
            }
        }


    }
</script>