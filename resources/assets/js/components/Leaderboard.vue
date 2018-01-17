<template>

    <div class="container is-fluid">
        <div class="container">
            <div>
                <canvas ref="canvas" id="canvas"></canvas>
            </div>
        </div>
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
                            <div class="field">
                                <label class="label" style="padding-top:7px;">Minimum Plays</label>
                                <div class="control">
                                    <input class="input" type="text" v-model="min_plays"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
            <div class="column is-three-quarters">
                <section class="panel">
                    <p class="panel-heading"> Leaderboard</p>
                    <div class="panel-block"  v-bind:class="{ 'is-loading' : loading}">

                    </div>
                    <div class="panel-block table-panel-body"  v-bind:class="{ 'is-loading' : loading}">
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
                            <tr v-for="(player,index) in filtered_player_list">
                                <th>{{index + 1}}</th>
                                <td><a v-bind:href="'/players/'+ player.id + '?group=' + group_value + '&season=' + season">{{player.name}}</a></td>
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
    import moment from 'moment';
    export default {
        data() {
            return {chart: null, players: [], groups: [], group_value: 1, season: 1, min_plays: 5, loading:true, chart_data: null}
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
        computed: {
            filtered_player_list: function() {
                return this.players.filter(
                    (player) => {
                        //TODO have filter only accept numbers
                        return player.play_count >= this.min_plays
                    }
                );
            }
        },
        mounted(){
            axios.post('/players/json', {
                'group': this.group_value,
                'season': this.season
            }).then(response => {
                this.loading=false;
                this.players = response.data.players;
                this.groups = response.data.groups;
                this.group_value = response.data.group;
                this.season = response.data.season;
                var ctx = document.getElementById('canvas').getContext('2d');
                var player_chart_data = response.data.players.map((player) => {
                    player.chart_data.map((entry) => {
                        entry.x = moment(entry.x).toDate();
                        return entry;
                    });
                    return {
                        label: player.name,
                        borderColor: "#" + ((1 << 24) * Math.random() | 0).toString(16),
                        fill: false,
                        data: player.chart_data
                    };
                });
                response.data.players.map((player) => {
                    player.chart_data.map((entry) => {
                        entry.x = moment(entry.x).toDate();
                        return entry;
                    });
                });
                this.chart = new Chart(ctx, {
                    type: "line",
                    data: {
                        datasets: player_chart_data
                    },
                    responsive: true,
                    options: {
                        elements: {
                            line: {
                                tension: 0
                            }
                        },
                        scales: {
                            xAxes: [{
                                type: 'time',
                                scaleLabel: {
                                    labelString: 'Date'
                                },
                                time: {
                                    displayFormats: {
                                        quarter: 'MMM YYYY'
                                    }
                                }
                            }],
                        }
                    }
                });
                console.log(c.data);
            })
        },
        methods: {
            removeData(chart) {
                chart.data.datasets.forEach((dataset) => {
                    dataset.data.pop();
                });
                chart.update();
            },
            addData(chart, data) {
                chart.data.datasets.forEach((dataset) => {
                    dataset.data.push(data);
                });
                chart.update();
            },
            submit() {
                console.log("posted", this.season, this.group_value);
                this.loading=true;
                axios.post('/players/json', {
                    'group': this.group_value,
                    'season': this.season
                }).then(response => {
                        this.loading = false;
                        console.log("receive", response.data);
                        //map chart data to moment format
                        var player_chart_data = response.data.players.map((player) => {
                            player.chart_data.map((entry) => {
                                entry.x = moment(entry.x).toDate();
                                return entry;
                            });
                            return {
                                label: player.name,
                                borderColor: "#" + ((1 << 24) * Math.random() | 0).toString(16),
                                fill: false,
                                data: player.chart_data
                            };
                        });
                        response.data.players.map((player) => {
                            player.chart_data.map((entry) => {
                                entry.x = moment(entry.x).toDate();
                                return entry;
                            });
                        })
                        this.chart_data = {
                            datasets: player_chart_data
                        };
                        this.players = response.data.players;
                        this.groups = response.data.groups;
                        this.group_value = response.data.group;
                        this.season = response.data.season;
                        var ctx = document.getElementById('canvas').getContext('2d');
                        var c = new Chart(ctx, {
                            type: "line",
                            data: this.chart_data,
                            responsive: true,
                            options: {
                                elements: {
                                    line: {
                                        tension: 0
                                    }
                                },
                                scales: {
                                    xAxes: [{
                                        type: 'time',
                                        scaleLabel: {
                                            labelString: 'Date'
                                        }
                                    }],
                                }
                            }
                        });
                        console.log(c.data, c.options);

                    }
                );
            }
        }
    }
</script>
<style>
    .container{
        width: 80%;
        margin: 20px auto;
    }
</style>