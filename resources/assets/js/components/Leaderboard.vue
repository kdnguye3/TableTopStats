<template>
    <div class="container is-fluid">
        <nav class="breadcrumb" aria-label="breadcrumbs" id="breadcrumbs-container">
            <ul>
                <li class="is-active"><a href="/players">Leaderboard</a></li>
            </ul>
        </nav>
        <div class="columns">
            <div class="column is-one-quarter">
                <section class="panel">
                    <p class="panel-heading">
                        Filters
                    </p>
                    <div class="panel-block">
                        <form class="control">
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
                                <label class="label" style="padding-top:7px;">Season</label>
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
                        </form>
                    </div>
                </section>
            </div>
            <div class="column">
                <section class="panel">
                    <p class="panel-heading"> Leaderboard</p>
                    <div class="panel-block leaderboard-panel-body">
                        <table class="table is-narrower leaderboard">
                            <thead>
                            <th>Place</th>
                            <th>Player Name</th>
                            <th>Plays</th>
                            <th>Wins</th>
                            <th>Win Rate</th>
                            <th>Adjusted Plays</th>
                            <th>Adjusted Wins</th>
                            <th>Adjusted Win Rate</th>
                            <th>Expected Win Rate</th>
                            <th>POE</th>
                            </thead>
                            <tr v-for="(player,index) in players">
                                <th>{{index + 1}}</th>
                                <td><a v-bind:href="'/players/'+ player.id">{{player.name}}</a></td>
                                <td>{{player.play_count}}</td>
                                <td>{{player.wins}}</td>
                                <td>{{player.win_rate | percent }}%</td>
                                <td>{{player.new_play_count | round}}</td>
                                <td>{{player.new_wins | round}}</td>
                                <td>{{player.new_win_rate | percent}}%</td>
                                <td>{{player.new_expected_win_rate | percent}}%</td>
                                <td>{{player.new_adjusted_win_rate | percent}}%</td>
                            </tr>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {players: [], groups: [], group_value: 0, season: 0}
        },
        filters: {
            percent: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value * 100 * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor;
            },
            round: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor;
            }
        },

        created() {
            axios.post('/players/json', {
                'group': this.group_value,
                'season': this.season
            }).then(response => {
                    this.players = response.data.players;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                }
            );
        },

        methods: {
            submit() {
                console.log("posted", this.season, this.group_value);
                axios.post('/players/json', {
                    'group': this.group_value,
                    'season': this.season
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